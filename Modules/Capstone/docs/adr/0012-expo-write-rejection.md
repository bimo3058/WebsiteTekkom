# Reject EXPO writes to the split assessment table

`AssessmentScoreController` accepted `EXPO` rows into the split table while the grade engine computes EXPO only from member self-evaluations, so those writes were silently ignored. We reject `EXPO` at validation (index + store) with a message pointing to the self-evaluation flow, and keep serving old rows from `summary` read-only for audit.

## Considered Options

- **Dual-write** (mirror EXPO submissions into self-evaluations): forks grade truth — two writers, two shapes, silent precedence rules for conflicts.
- **Migrate-and-delete** (move old rows, drop EXPO everywhere): destroys the audit trail the summary still serves.
