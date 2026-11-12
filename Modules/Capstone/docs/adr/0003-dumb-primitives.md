# Dumb primitives, gating in wrappers

Generic `button/card/alert/badge/input` carry no `BladeFeatureAccess` logic. Only `sb-link` and `feature-link` call `reason()` and render `aria-disabled + title`. We chose this split so gating changes stay in two files instead of drifting across every primitive.
