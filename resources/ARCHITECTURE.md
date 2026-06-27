# Frontend Skeleton (Scalable)

This structure keeps style, behavior, and view templates separated for long-term scalability.

## Directory layout

- `resources/views/`
  - Blade templates only (markup and minimal Blade conditionals)
  - Avoid long inline `<style>` and large inline JS objects
- `resources/css/`
  - `base/`: reset, typography, tokens
  - `components/`: reusable UI blocks (cards, buttons, nav)
  - `pages/`: page-specific styles when component composition is not enough
  - `themes/`: global theme files (light/dark, student/admin scope)
- `resources/js/`
  - `modules/`: reusable Alpine/Vanilla modules
  - `components/`: interactive UI components
  - `services/`: API or helper services
  - `stores/`: shared state stores

## Current implementation

- Student theme CSS moved to `resources/css/themes/student-shell.css`
- Student nav theme toggle state moved to `resources/js/modules/student-theme.js`
- Alpine registration centralized in `resources/js/app.js`

## Team rules

1. Keep Blade focused on structure and data binding.
2. Put reusable JS state/logic into `resources/js/modules` or `resources/js/components`.
3. Put reusable styling into `resources/css/components`; avoid large inline styles.
4. Keep page-specific exceptions in `resources/css/pages`.
5. Keep naming consistent and scoped (`student-*`, `admin-*`) to avoid style collisions.
6. Run `npm run build` after refactors.
