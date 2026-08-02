# Translation Guide & Glossary

Laravel locale files live in `resources/lang/{ar,en,fr,es}/`. `ar` (Arabic) is the
**reference locale** — every key present in `ar` must exist in `en`, `fr` and `es`.

## Parity rules

- All 6 files (`actions.php`, `app.php`, `attributes.php`, `auth.php`,
  `pagination.php`, `validation.php`) must have the **exact same key set** in every
  locale. Guarded by `tests/Unit/LangParityTest.php` — run `php artisan test tests/Unit/LangParityTest.php`.
- Never leave a value empty. Never drop a `:placeholder`.
- Only touch `ar` to fix corruption (e.g. garbage/foreign characters), never for wording changes.
- Nested keys (`validation.attributes.*`) flatten with `.` for the parity check.

## Core glossary (ar / en / fr / es)

| Key (app.php) | ar | en | fr | es |
|---|---|---|---|---|
| `submissions` | التقديمات | Submissions | Soumissions | Envíos |
| `submission_created` | تم إنشاء المشاركة بنجاح. | Submission created successfully. | Soumission créée avec succès. | Propuesta creada correctamente. |
| `judges` | الحكام | Judges | Juges | Jurados |
| `round` | الجولة :number | Round :number | Tour :number | Ronda :number |
| `standings` | الترتيب | Standings | Classement | Clasificación |
| `domain` / `domains` | المجال / المجالات | Domain / Domains | Domaine / Domaines | Dominio / Dominios |

## Register conventions

- **Spanish**: informal singular "tú" everywhere — *Ingresa*, *Intenta*, *Crea*, *Elige*.
  Do not mix with "usted" forms (*Ingrese*, *Intente*, *Cree*, *Elija*).
- **French**: standard "vous" forms in titles/CTAs (e.g. *Choisissez*, *Configurez*, *Concourez*).

## Domain-specific terms

- Injury-return status (`returned_status` / `status_returned`): ar عاد · en Returned · fr De retour · es Regresado
- Clean sheets: ar شباك نظيفة · en Clean Sheets · fr Cages inviolées · es Portería a cero
- Goal difference: ar فارق الأهداف · en Goal Difference · fr Différence de buts · es Diferencia de goles
- Injury / stoppage time (`added_time`): ar الوقت المضاف · en Added Time · fr Temps additionnel · es Tiempo añadido
- Medical record type (`record_type`): ar نوع السجل · en Record Type · fr Type de dossier · es Tipo de registro
- Submit/send actions: ar إرسال · en Submit · fr Soumettre · es Enviar
- Add/Create actions: ar إضافة · en Add · fr Ajouter · es Agregar

## Known caveats

- `resources/lang/vendor/status-kit/` is a published vendor package and contains legacy
  mojibake (`�?`); it is **out of scope** for parity and should not be edited directly.
- `ar/validation.php` spells out certain rules without the `:values` placeholder
  (e.g. `required_with_all`); en/fr/es may use `:values` naturally — Laravel resolves it.
