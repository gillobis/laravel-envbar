# How to Contribute to laravel-env-bar

Thank you for wanting to contribute! Please read these guidelines before opening a PR.

## Workflow

1. Fork the repository
2. Create a **branch** with a descriptive name:
   - `feat/environment-switcher`
   - `fix/ajax-injection-bug`
   - `docs/update-readme`
3. Write the code following the rules below
4. Open a **Pull Request** towards `main`

## Development Rules

- **PHP 8.2+** — do not use features from higher versions without updating the requirements
- **PSR-12** — format with `./vendor/bin/pint` before each commit
- **Mandatory Tests** — every new feature must have tests; every bug fix must have a regression test
- **CHANGELOG** — add a line in the `[Unreleased]` section of `CHANGELOG.md`

## Branch Naming Convention

| Type           | Prefix     | Example                        |
|----------------|------------|-------------------------------|
| New Feature    | `feat/`    | `feat/favicon-overlay`        |
| Bug Fix        | `fix/`     | `fix/production-check`        |
| Documentation  | `docs/`    | `docs/configuration-options`  |
| Refactoring    | `refactor/`| `refactor/middleware-logic`   |
| CI / tooling   | `chore/`   | `chore/update-pest`           |

## Commit convention (Conventional Commits)

Use the format:

> type(scope): short description in English
> 
> feat(middleware): add support for AJAX requests
> fix(config): fix default value for position
> docs(readme): add switcher example
> chore(ci): update PHP 8.3 matrix

## Breaking changes

If your PR introduces a breaking change:
- Document it clearly in the PR
- Update `CHANGELOG.md` with the `### Breaking Changes` section
- Indicate that a MAJOR version bump is required

## Reporting bugs

Open an **Issue** using the "Bug report" template before opening a PR.