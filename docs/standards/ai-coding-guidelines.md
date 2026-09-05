
# uBix AI Coding Guidelines

## 1. Foundation & Context
- **Index the Codebase:** Always have the AI index or review the relevant codebase and documentation before starting. This ensures context-aware suggestions and reduces errors.
- **Set Global and Project Rules:** Define coding standards (e.g., SOLID, PSR, naming conventions) and project-specific preferences up front. Reference these in your session so you don't have to repeat them in every prompt.

## 2. Writing Effective Prompts
- **Be Specific and Concrete:**
	- Instead of: "Make button toggle."
	- Use: "Add a boolean field to the users table, expose it through the /api/users/:id endpoint, and conditionally render the EditButton component based on that field."
- **Prompt Template:**
	```
	Implement (function/class/endpoint) to (goal) using (lib/framework).
	Work in (file/paths) only.
	Respect (style/tests/rules).
	Provide (tests/docs/migrations).
	If assumptions are needed, list them first.
	```
- **Model Selection:**
	- Specify which AI model(s) to use for planning and coding.
	- Do not switch models mid-conversation unless necessary and clearly communicated.

## 3. Coding Approach
- **No "Vibe" Coding:**
	- Treat the AI as a senior engineer, not a code generator. Expect architectural awareness, pattern usage, and codebase familiarity.
- **Work in Small, Testable Steps:**
	- Break tasks into digestible, sprint-sized pieces.
	- Example: "Plan and implement a tool favorites/bookmarks feature. Users should be able to bookmark tools and view their favorites on a dedicated page."
- **Plan Before Coding:**
	- Require a detailed implementation plan before any code is written.
	- Consider:
		- Data flow (storage, retrieval, updates)
		- Key functions/classes/components
		- Potential challenges (auth, performance, UX)
		- Integration points (where UI elements appear, navigation updates)
	- Example: "Create a step-by-step plan for adding a favorites feature, including data model changes, API endpoints, and UI updates."

## 4. Collaboration & Review
- **Pair Programming Mindset:**
	- Don't blindly accept AI output. Review, question, and edit as you would with a human collaborator.
	- Ask: "Why did you choose this pattern? What are the error cases? How does this impact performance?"
- **Iterative Improvement:**
	- Edit the code as needed. Let the AI refactor or adapt around your changes.
- **Collaboration, Not Delegation:**
	- Use the AI as a partner, not a replacement. Maintain ownership of the code and decisions.

## 5. Additional Recommendations
- **Security:** Always prompt for and review security considerations (e.g., input validation, XSS, SQL injection).
- **Testing:** Require the AI to generate or update tests for all new code.
- **Documentation:** Ask for doc updates or inline comments for complex logic.
- **Error Handling:** Explicitly request error and edge case handling in prompts.
- **Performance:** Ask the AI to consider and explain performance implications for non-trivial features.
- **Sensitive Data & Privacy:** Never expose credentials, secrets, or sensitive data in prompts or code. Ensure privacy best practices are followed.
- **Code Review & Merge Process:** AI-generated code lands on `dev` **only through a merge request** (see [`branching-and-git-workflow.md` → One land path: MR-only `dev`](branching-and-git-workflow.md#one-land-path-mr-only-dev-2026-07-30)) — the old direct-merge fast-path is retired (2026-07-30; no exceptions, Christopher included). The MR pipeline's Claude review posts each finding as a **blocking discussion thread**: fix-and-resolve or resolve-with-a-written-dismissal every one before merge; a human approves. The full `code:review` gate (every tool — see CLAUDE.md for the current list) still runs at the pre-push hook on the feature branch. Gate/config changes always need Christopher Olsen's sign-off first.
- **Audit & Traceability:** Periodically audit AI-generated code for maintainability and security. Keep a changelog or record of AI-generated changes for traceability.

---

## 6. Engineer I Guidelines (GitHub Copilot in VS Code)

Engineer I engineers primarily use GitHub Copilot for inline autocomplete and basic chat assistance. The focus is on building good habits around reviewing AI suggestions, not getting overwhelmed by advanced prompting techniques.

### Understand What Copilot Is (and Isn't)
- Copilot is an autocomplete tool, not a reasoning engine. It predicts what code likely comes next based on context — it does not understand your architecture or business logic.
- It does not remember previous suggestions or conversations. Every suggestion is based only on what is currently visible in your editor.
- Treat suggestions as a starting point, never as a finished answer.

### Write Intent Before Accepting Suggestions
- Before letting Copilot fill in a function, write a clear function signature and a comment describing what it should do. This significantly improves suggestion quality.
- Example:
	```php
	// Returns the total price of all active line items, excluding cancelled orders
	public function calculateActiveTotal(Order $order): float
	```
- If you don't know what the function should do well enough to write that comment, don't accept Copilot's suggestion yet — ask your team lead first.

### Review Every Line Before Accepting
- Press `Tab` only when you understand what the suggestion does and why.
- If a suggestion looks right but you can't explain it, do not accept it. Ask a senior engineer or look it up.
- Copilot will confidently suggest methods and functions that do not exist. Always verify against actual documentation or the codebase before using them.

### Security Rules (Non-Negotiable)
- Never accept autocomplete for database queries, authentication logic, or permission checks without careful review.
- Never let Copilot fill in API keys, passwords, tokens, or any credentials — even as placeholders.
- If Copilot suggests raw SQL or string-interpolated queries, reject it. Always use parameterized queries or the ORM.

### Testing
- Do not skip writing tests just because Copilot generated the implementation. AI-generated code needs tests just like hand-written code.
- You can use Copilot to help scaffold tests, but verify that the test actually covers the logic and would catch a bug.

### When to Ask a Senior
- If you accepted a suggestion and aren't sure it's correct, flag it in your PR and ask.
- If Copilot is steering you toward a pattern you haven't seen in the codebase before, stop and ask before continuing.
- When in doubt, don't Tab. Ask.

---

## 7. Engineer II Guidelines (Copilot + Chat AI)

Engineer II engineers are expected to use both Copilot for inline assistance and chat-based AI (e.g., Claude, GitHub Copilot Chat) for more complex tasks. The focus is on using AI effectively within a feature or component scope, with appropriate review and judgment.

### Use the Right Tool for the Task
- Use **Copilot autocomplete** for boilerplate, repetitive patterns, and filling in known structures.
- Use **chat AI** (Claude, Copilot Chat) for explaining unfamiliar code, drafting implementations, debugging, or writing tests for complex logic.
- Don't use chat AI as a shortcut to avoid understanding the problem. Use it to move faster once you understand what you need.

### Prompt with Context
- Before asking chat AI for help, give it the relevant context: what the feature does, what files are involved, what constraints exist.
- Use the prompt template from Section 2 as a starting point.
- Scope your requests to specific files or components. Avoid vague prompts like "fix my controller" — instead say "In `OrderController.php`, the `store()` method is not validating the `quantity` field before saving. Add validation using the existing Form Request pattern."

### Plan First for Non-Trivial Work
- For any task that spans more than one file or touches shared logic, ask the AI for a plan before asking for code.
- Review the plan with your team lead if it touches authentication, data models, or external integrations.
- Do not let the AI proceed to code if the plan doesn't match your understanding of the system.

### Validate AI Output Against the Codebase
- Check that the patterns, class names, and methods the AI suggests actually exist and are used consistently in the project.
- AI will sometimes suggest a reasonable pattern that isn't the one your codebase uses. Consistency matters — match existing conventions.

### Code Review Responsibilities
- AI-generated code is your code. You are responsible for it in review.
- Be prepared to explain every line in a PR review. "Copilot wrote it" is not an acceptable explanation.
- Flag AI-generated sections in your PR description if they are non-trivial, so reviewers know to look more closely.

### Security & Testing
- Follow all security rules from Section 5.
- For any AI-generated logic touching auth, payments, or user data, explicitly ask the AI: "What are the security risks here?" and address them before submitting.
- Require tests for all AI-generated implementations. Use chat AI to help write them if needed, but verify coverage is meaningful.

---

## 8. Engineer III Guidelines (Advanced AI-Assisted Development)

Engineer III engineers are expected to apply the full guidelines in Sections 1–5 and take on additional responsibility for setting standards, reviewing AI-generated code from junior engineers, and using AI for higher-order tasks like architecture, refactoring, and cross-cutting concerns.

### Lead with Architecture Awareness
- Before using AI for any significant feature, ensure the AI has full context: relevant files, data models, existing patterns, and constraints.
- Use AI to stress-test your architectural decisions: "What are the downsides of this approach? What would break if we scaled this to 10x traffic?"
- Do not let AI drive architectural decisions. Use it to validate, challenge, and refine your own thinking.

### Use AI for Higher-Order Tasks
- Refactoring: Ask the AI to identify code smells, suggest consolidations, and propose cleaner abstractions — then review and decide.
- Cross-cutting concerns: Use AI to audit consistency across files (e.g., "Are all controllers following the same error handling pattern?").
- Documentation and onboarding: Use AI to generate or improve technical documentation, ADRs, and onboarding materials.
- Code review assistance: Use AI as a first-pass reviewer on your own PRs before submitting. Ask it to find edge cases, security gaps, or missing tests.

### Mentor Junior Engineers on AI Use
- When reviewing PRs from Engineer I/II, look for signs of uncritical AI acceptance: unexplained patterns, hallucinated methods, missing tests, or inconsistent conventions.
- Use these as teaching moments, not just corrections. Help them understand why the suggestion was wrong or risky.
- Share effective prompting techniques and review habits with your team.

### Maintain Standards and Traceability
- For significant AI-assisted changes, document the intent and key decisions in the PR or commit message — not just what changed, but why.
- Periodically audit AI-heavy areas of the codebase for maintainability and security drift.
- If you identify a pattern that AI tools consistently get wrong in your codebase, document it in the project rules so it can be referenced in future sessions.

### Security & Review Ownership
- You are the last line of defense for AI-generated code merging into the codebase. Apply Section 5 rigorously.
- For any AI-generated changes to auth, permissions, data access, or external integrations, treat them with the same scrutiny as a change from an unknown contributor.
