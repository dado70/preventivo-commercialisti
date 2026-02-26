/**
 * Preventivo Commercialisti - JavaScript principale
 * Copyright (C) 2024 Alessandro Scapuzzi <dado70@gmail.com>
 * Licenza: GPL v3
 */

'use strict';

// -------------------------------------------------------
// Auto-dismiss degli alert dopo 5 secondi
// -------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const alerts = document.querySelectorAll('.alert-dismissible.fade.show');
    alerts.forEach(function (alert) {
        setTimeout(function () {
            const bsAlert = bootstrap.Alert.getOrCreateInstance(alert);
            if (bsAlert) bsAlert.close();
        }, 5000);
    });
});

// -------------------------------------------------------
// Conferma eliminazione generica
// -------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-confirm]').forEach(function (el) {
        el.addEventListener('click', function (e) {
            const msg = el.getAttribute('data-confirm') || 'Sei sicuro di voler procedere?';
            if (!confirm(msg)) {
                e.preventDefault();
                e.stopPropagation();
            }
        });
    });
});

// -------------------------------------------------------
// Filtro tabella in-page (data-filter-table)
// -------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-filter-input]').forEach(function (input) {
        const targetId = input.getAttribute('data-filter-input');
        const table = document.getElementById(targetId);
        if (!table) return;

        input.addEventListener('keyup', function () {
            const val = input.value.toLowerCase();
            const rows = table.querySelectorAll('tbody tr');
            let visible = 0;
            rows.forEach(function (row) {
                const text = row.textContent.toLowerCase();
                const show = text.includes(val);
                row.style.display = show ? '' : 'none';
                if (show) visible++;
            });

            // Aggiorna contatore se presente
            const counter = document.querySelector('[data-filter-count="' + targetId + '"]');
            if (counter) counter.textContent = visible;
        });
    });
});

// -------------------------------------------------------
// Formattazione importi euro (input number -> display)
// -------------------------------------------------------
function formatEuro(value) {
    const num = parseFloat(value) || 0;
    return '€\u00a0' + num.toFixed(2).replace('.', ',').replace(/\B(?=(\d{3})+(?!\d))/g, '.');
}

// -------------------------------------------------------
// Helpers per il form preventivo
// (le funzioni principali sono inline nella view per accedere
//  alle variabili PHP, ma qui mettiamo utility condivise)
// -------------------------------------------------------
window.preventivoUtils = {
    formatEuro: formatEuro,

    /** Aggiorna la classe di evidenziazione dello stato */
    aggiornaStatoBadge: function (selectEl) {
        const badges = {
            'bozza': 'secondary',
            'inviato': 'info',
            'accettato': 'success',
            'rifiutato': 'danger',
            'scaduto': 'warning'
        };
        const badge = document.querySelector('#stato-badge');
        if (badge) {
            badge.className = 'badge bg-' + (badges[selectEl.value] || 'secondary');
            badge.textContent = selectEl.options[selectEl.selectedIndex].text;
        }
    }
};

// -------------------------------------------------------
// Toggle password visibility (generico)
// -------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('.toggle-pw').forEach(function (btn) {
        btn.addEventListener('click', function () {
            const targetId = btn.getAttribute('data-target');
            const input = document.getElementById(targetId);
            if (!input) return;
            if (input.type === 'password') {
                input.type = 'text';
                btn.innerHTML = '<i class="bi bi-eye-slash"></i>';
            } else {
                input.type = 'password';
                btn.innerHTML = '<i class="bi bi-eye"></i>';
            }
        });
    });
});

// -------------------------------------------------------
// Validazione password in tempo reale
// -------------------------------------------------------
document.addEventListener('DOMContentLoaded', function () {
    const pwInput = document.getElementById('password_nuova') || document.getElementById('password');
    if (!pwInput) return;

    const reqLength = document.getElementById('req-length');
    const reqUpper  = document.getElementById('req-upper');
    const reqNumber = document.getElementById('req-number');

    pwInput.addEventListener('input', function () {
        const val = pwInput.value;
        if (reqLength) reqLength.classList.toggle('text-success', val.length >= 8);
        if (reqLength) reqLength.classList.toggle('text-muted', val.length < 8);
        if (reqUpper)  reqUpper.classList.toggle('text-success', /[A-Z]/.test(val));
        if (reqUpper)  reqUpper.classList.toggle('text-muted', !/[A-Z]/.test(val));
        if (reqNumber) reqNumber.classList.toggle('text-success', /[0-9]/.test(val));
        if (reqNumber) reqNumber.classList.toggle('text-muted', !/[0-9]/.test(val));
    });
});
