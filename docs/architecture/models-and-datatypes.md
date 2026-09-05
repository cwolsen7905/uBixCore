# Models and DataTypes

## Overview

uBix Core models represent database rows as PHP objects. They enforce three rules:

1. **No raw PHP scalar types** — every property uses a custom DataType class
2. **`null` means "not loaded"** — a null property means no value was provided; the database default applies
3. **Change tracking** — models track which properties have been set so repositories can build efficient INSERT and UPDATE queries

---

## DataType System

DataTypes are typed wrappers around a single value. They live in `php/Ubix/DataType/` and carry validation constraints via Symfony Validator annotations.

### Nullability is in the DataType, not the property

Whether a database column accepts `NULL` is expressed by the DataType class, not by a `?` on the model property parameter:

| Situation | DataType to use |
|---|---|
| DB column is `NOT NULL` | `Varchar` |
| DB column allows `NULL` | `NullableVarchar` |

```php
// Column is NOT NULL — use Varchar
private ?Varchar $username = null;

// Column allows NULL — use NullableVarchar
private ?NullableVarchar $nickname = null;
```

The `?` on the property itself is always present, but it means **"not loaded"**, not **"nullable column"**.

### Why this matters

A property being `null` and a column holding `NULL` are two different things:

| State | Meaning | Repository action |
|---|---|---|
| `$model->username === null` | Property was not loaded | Skip column in query |
| `$model->nickname instanceof NullableVarchar` (value is null internally) | Column value is explicitly `NULL` | Write `NULL` to database |

If nullability were tracked on the property with `?Varchar`, these two states would be indistinguishable.

---

## Model Design

### Properties

Every property is typed as `?DataType = null`. The `?` and default `null` together mean "not loaded / not set". A non-null value always holds a DataType instance.

```php
private ?Varchar $username = null;           // NOT NULL column, not loaded yet
private ?NullableVarchar $nickname = null;   // NULL-able column, not loaded yet
```

### Constructor

The constructor accepts all properties as optional parameters defaulting to `null`. After assigning them, it calls `markNonNullChanged()` to mark only the properties that were explicitly provided.

```php
public function __construct(
    private ?AffiliateId $id = null,
    private ?Varchar $username = null,
    private ?NullableVarchar $nickname = null,
) {
    $this->markNonNullChanged();
}
```

This serves two use cases with the same constructor:

**New record (INSERT):**
```php
$affiliate = new Affiliate(username: new Varchar('john'));
// Only 'username' is marked changed — repository inserts only that column
// DB handles defaults for everything else
```

**Hydration from SELECT:**
```php
$affiliate = new Affiliate(
    id:       new AffiliateId(42),
    username: new Varchar('john'),
    nickname: new NullableVarchar(null),
);
// All three columns are marked changed after construction
// Repository MUST call clearChanges() immediately after — see Repository section
```

### Setters

Setters accept a **non-nullable** DataType parameter. They must never accept `null` because `null` is not a value — it means "not loaded", and there is no use case for a setter to put a property back into the unloaded state.

```php
// Correct — parameter is non-nullable
public function setUsername(Varchar $username): void
{
    $this->username = $username;
    $this->markChanged('username');
}

// Wrong — do not do this
public function setUsername(?Varchar $username): void { ... }
```

If you need to write `NULL` to a nullable column, pass a `NullableVarchar` whose internal value is null:

```php
$affiliate->setNickname(new NullableVarchar(null));
// 'nickname' is marked changed, repository writes NULL to the column
```

### Change tracking

`AbstractModel` provides:

| Method | Description |
|---|---|
| `markNonNullChanged()` | Mark all non-null properties as changed. Use in constructors. |
| `markChanged(string $property)` | Mark a single property as changed. Used by setters. |
| `hasChanged(string $property)` | Check if a specific property has changed. |
| `getChangedProperties()` | Return all changed property names. Used by repositories. |
| `clearChanges()` | Reset change tracking. Repositories call this after hydration. |

---

## Repository Integration

### INSERT (new record)

Build the column list and values from `getChangedProperties()`. Properties not in the list are omitted so the database default applies.

```php
$affiliate = new Affiliate(username: new Varchar('john'));
// getChangedProperties() returns ['username']
// INSERT INTO affiliates (username) VALUES (?)
```

### SELECT → hydration

Construct the model using all columns returned by the query, then **immediately call `clearChanges()`**. This resets the baseline so only subsequent setter calls are tracked as changes.

```php
$affiliate = new Affiliate(
    id:       new AffiliateId($row['id']),
    username: new Varchar($row['username']),
    nickname: new NullableVarchar($row['nickname']),
);
$affiliate->clearChanges(); // Required — resets baseline after hydration
```

Omitting `clearChanges()` means `getChangedProperties()` still contains all hydrated columns, and the next UPDATE will write every column instead of only what changed.

### UPDATE

After hydration and `clearChanges()`, only setters called by the service layer will appear in `getChangedProperties()`. Build the SET clause from that list only.

```php
$affiliate->setUsername(new Varchar('jane'));
// getChangedProperties() returns ['username']
// UPDATE affiliates SET username = ? WHERE id = ?
```

### Partial hydration

Repositories may SELECT only a subset of columns. Columns not in the SELECT are simply not passed to the constructor and remain `null` (not loaded). This is safe — those properties will never appear in `getChangedProperties()` and will never be written by an UPDATE.

### Concurrency

Column-scoped UPDATEs built from `getChangedProperties()` are strictly safer than full-row writes: two concurrent writers touching disjoint columns cannot clobber each other's changes. Change tracking does **not** introduce race conditions relative to the full-row alternative.

It also does not remove the need for locking when the write's correctness depends on the row's state at read time. Two cases still require upstream coordination (`SELECT ... FOR UPDATE`, an application-level mutex, or an optimistic-concurrency version column) — and they require it regardless of whether the save is partial or full:

- **Read-modify-write on the same column** — e.g. `balance = balance - 100`. Two readers both see `balance = 1000`, both write `balance = 900`, one decrement is lost.
- **Cross-column invariants** — e.g. `start_date < end_date`. Writer A sets `start_date`, writer B sets `end_date` without seeing A's value; the combined row can violate the invariant even though neither writer's own UPDATE was wrong.

**Rule:** if a write's correctness depends on the row's current state, the caller must lock. If the write is an unconditional assignment (set this field to this value), `markChanged` + a column-scoped UPDATE is sufficient on its own.

---

## Summary of rules

1. Use `Varchar` for NOT NULL columns, `NullableVarchar` for nullable columns — never `?Varchar`
2. Model properties are always `?DataType = null` — the `?` means "not loaded", nothing else
3. Setter parameters are always non-nullable DataType — never `?DataType`
4. Constructors call `markNonNullChanged()` — never `markAllChanged()`
5. Repositories call `clearChanges()` immediately after constructing a hydrated model
6. Repositories build INSERT column lists and UPDATE SET clauses from `getChangedProperties()` only
