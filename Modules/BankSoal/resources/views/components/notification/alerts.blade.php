@php
    $flashSuccess = Session::get('success');
    $flashError = Session::get('error');
    $validationErrors = $errors->all();
@endphp

@if ($flashSuccess || $flashError || count($validationErrors))
    <div
        id="banksoal-flash-snackbar-data"
        data-success='@json($flashSuccess)'
        data-error='@json($flashError)'
        data-validation-errors='@json($validationErrors)'
        hidden
    ></div>

    <script>
        (function () {
            let hasShownFlashSnackbar = false;

            function escapeHtml(text) {
                return String(text)
                    .replace(/&/g, '&amp;')
                    .replace(/</g, '&lt;')
                    .replace(/>/g, '&gt;')
                    .replace(/"/g, '&quot;')
                    .replace(/'/g, '&#039;');
            }

            function safeJsonParse(value, fallback) {
                try {
                    return JSON.parse(value);
                } catch (e) {
                    return fallback;
                }
            }

            function showFlashSnackbar() {
                if (hasShownFlashSnackbar) return true;

                if (typeof Snackbar === 'undefined' || typeof Snackbar.show !== 'function') {
                    return false;
                }

                const flashData = document.getElementById('banksoal-flash-snackbar-data');
                if (!flashData) return true;

                const flashSuccess = safeJsonParse(flashData.dataset.success || 'null', null);
                const flashError = safeJsonParse(flashData.dataset.error || 'null', null);
                const validationErrors = safeJsonParse(flashData.dataset.validationErrors || '[]', []);

                if (flashSuccess) {
                    Snackbar.show(escapeHtml(flashSuccess), 'success', 4000);
                }

                if (flashError) {
                    Snackbar.show(escapeHtml(flashError), 'error', 5000);
                }

                if (validationErrors && validationErrors.length) {
                    const list = validationErrors
                        .map((err) => `<li>${escapeHtml(err)}</li>`)
                        .join('');
                    Snackbar.show(`Validasi gagal:<ul>${list}</ul>`, 'error', 5000);
                }

                hasShownFlashSnackbar = true;
                return true;
            }

            function showFlashSnackbarWithRetry(maxRetry = 30, intervalMs = 100) {
                let attempt = 0;
                const tryShow = () => {
                    const shown = showFlashSnackbar();
                    if (shown) return;

                    attempt += 1;
                    if (attempt < maxRetry) {
                        window.setTimeout(tryShow, intervalMs);
                    }
                };

                tryShow();
            }

            if (document.readyState === 'loading') {
                document.addEventListener('DOMContentLoaded', function () {
                    showFlashSnackbarWithRetry();
                });
            } else {
                showFlashSnackbarWithRetry();
            }
        })();
    </script>
@endif
