# Experiment bucketing and feature flags

> **Pattern reference, not an implementation.** uBixCore ships no experiment runner. This is
> the schema and the reasoning behind it, for hosts that need to put a portion of their users
> on a different code path and measure what happens. Take what fits.

An experiment here means: a named **feature**, split into **groups**, with each user assigned
to exactly one group and that assignment recorded, so later queries can attribute behaviour
to it.

## The three moving parts

**Definition** — one row per group of a feature. Several rows share a `feature` label; each
carries the percentage of users it should receive and any targeting that narrows it.

**Assignment** — one row per subject per feature, written when the subject is first bucketed.
Not a column on the user table (see below).

**Events** — an append-only log of what bucketed subjects did, keyed by group, for whoever
analyses the result.

## Definition table

```sql
CREATE TABLE Experiment_Groups (
  id                           INT UNSIGNED     NOT NULL AUTO_INCREMENT,
  feature                      VARCHAR(40)      NOT NULL COMMENT 'feature label; the groups of one experiment share it',
  audience                     VARCHAR(40)      NOT NULL DEFAULT '' COMMENT 'empty = everyone; else a host-defined segment',
  host                         VARCHAR(255)     NOT NULL DEFAULT '' COMMENT 'empty = all hosts; else restrict to this one',
  user_type                    VARCHAR(20)      NOT NULL DEFAULT '' COMMENT 'empty = all; else a host-defined tier',
  group_marker                 VARCHAR(20)      NOT NULL COMMENT 'A / B / control — what the assignment records',
  feature_state                TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'the variant this group receives',
  split                        TINYINT UNSIGNED NOT NULL DEFAULT 0 COMMENT '0-100, percent of eligible subjects in this group',
  internal_start_datetime      DATETIME         DEFAULT NULL,
  internal_bucket_end_datetime DATETIME         DEFAULT NULL,
  internal_end_datetime        DATETIME         DEFAULT NULL,
  external_start_datetime      DATETIME         DEFAULT NULL,
  external_bucket_end_datetime DATETIME         DEFAULT NULL,
  external_end_datetime        DATETIME         DEFAULT NULL,
  description                  TEXT             NOT NULL,
  PRIMARY KEY (id),
  UNIQUE KEY feature_audience_host_group (feature, audience, host, user_type, group_marker),
  KEY feature_group (feature, group_marker)
) ENGINE=InnoDB;
```

### Why six timestamps rather than a flag and one date

An experiment has three distinct moments, and they are not the same moment:

1. **start** — bucketing and delivery may begin.
2. **bucket end** — stop adding *new* subjects; everyone already in keeps their variant.
3. **end** — stop delivering at all, leaving the assignment rows intact for analysis.

Separating 2 from 3 is what lets an experiment wind down two different ways. Abandoning a
losing variant means stopping delivery. Concluding a winning one means stopping intake while
existing users keep the behaviour until it ships for everyone.

The obvious design is one set of three, plus an `internal_only` flag to gate staff testing.
It does not survive a multi-stage experiment: flipping that flag to "everyone" is a one-way
door, so there is no way to test the next stage internally while the current one runs
externally. Two independent sets of three cost four columns and remove the problem — and they
stay *true* start times, never rewritten at launch.

## Assignment table

```sql
CREATE TABLE Experiment_Users (
  id              INT UNSIGNED NOT NULL AUTO_INCREMENT,
  subject_type    VARCHAR(20)  NOT NULL COMMENT 'account | guest | fingerprint | email',
  subject_value   VARCHAR(64)  NOT NULL COMMENT 'the identifier of that type',
  group_id        INT UNSIGNED NOT NULL COMMENT 'FK -> Experiment_Groups.id',
  group_marker    VARCHAR(20)  NOT NULL COMMENT 'denormalised so reports need no join',
  bucket_event    VARCHAR(20)  NOT NULL DEFAULT '' COMMENT 'what caused bucketing: registration, login, page view…',
  bucket_event_id INT UNSIGNED DEFAULT NULL COMMENT 'FK into whichever table bucket_event names',
  datetime        DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  UNIQUE KEY subject_group (subject_type, subject_value, group_id)
) ENGINE=InnoDB;
```

**Do not store the assignment in spare columns on the user table.** Reusing generic
`udf01`/`udf02`-style columns is the common shortcut, and it fails three ways: the columns run
out, two experiments collide in one column, and answering "who was in variant B" means
remembering which column that experiment happened to use. A row per subject per feature costs
an insert and removes all three.

`subject_type` + `subject_value`, rather than a plain `user_id`, is what lets an experiment
cover signed-out traffic: bucket on a guest identifier or a fingerprint before an account
exists, and the row shape does not change once one does.

The unique key is the guarantee that matters — **one marker per subject per experiment**. A
subject cannot drift between variants and quietly poison the measurement.

## Event log

```sql
CREATE TABLE Experiment_Events (
  id            INT UNSIGNED NOT NULL AUTO_INCREMENT,
  group_id      INT UNSIGNED NOT NULL DEFAULT 0 COMMENT 'FK -> Experiment_Groups.id',
  event_type    VARCHAR(20)  NOT NULL DEFAULT '' COMMENT 'meaning is specific to the feature',
  subject_type  VARCHAR(20)  NOT NULL DEFAULT '',
  subject_value VARCHAR(64)  DEFAULT NULL,
  feature_state VARCHAR(8)   NOT NULL DEFAULT '',
  event_value   MEDIUMINT    NOT NULL DEFAULT 0,
  datetime      DATETIME     NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (id),
  KEY group_state (group_id, feature_state),
  KEY group_event_state (group_id, event_type, feature_state)
) ENGINE=InnoDB;
```

Deliberately generic: event types belong to the feature, not to the framework. Analysis joins
this to the definition row, so a report never needs to know how bucketing worked.

## Where the code touches it

Keep the contact points few. There are only two kinds:

**Bucketing** — the moment a subject is assigned. Registration, login, and first page view for
signed-out traffic cover nearly everything. Each writes the assignment row and puts the
resulting marker in the session.

**Reading** — consumers ask the session for the marker and branch on it. Nothing downstream
queries the assignment table directly.

That split is what makes the storage swappable: moving from columns-on-the-user-table to an
assignment table changes only the bucketing code, because consumers were reading the session
all along. Keep the shared logic in one service, and put feature-specific behaviour with the
feature it controls rather than in that service.

## Migrating to this later than you should have

Backfill assignment rows from the existing columns, switch reads to the new table, then drop
the columns in a separate migration once nothing reads them. Three steps, each separately
revertible; combined into one, none of them are.
