# SELLINAR — Brand Code & Design System

> Tento súbor je jediný zdroj pravdy pre vizuálnu identitu projektu Sellinar. Každý komponent, stránka a UI element musí byť konzistentný s týmito pravidlami. Ak niečo nie je definované, rozhoduj sa v duchu existujúcich pravidiel.

---

## 1. IDENTITA

- **Názov**: Sellinar
- **Výslovnosť**: \[selinár\]
- **Wordmark**: "Sell" (violet 600) + "inar" (ink) — vždy spolu, nikdy oddelené
- **Tagline**: "Webináre, ktoré predávajú. Na autopilote."
- **Ikona / Favicon**: Písmeno "S" v Space Grotesk Bold, biele na violet gradient pozadí (135°, #6C3AED → #7C4DFF), border-radius 12px

### Pravidlá loga
- Minimálna veľkosť wordmarku: 120px šírka
- Minimálna veľkosť ikony: 24×24px
- Ochranná zóna: výška písmena "S" na každú stranu
- Nikdy nedeformovať, nerotovať, nemeniť farby mimo definovaných variantov
- Tri varianty: light bg (violet+ink), dark bg (violet+white), accent bg (all white)

---

## 2. FARBY

### Primárna paleta

```css
:root {
  /* Primary — Violet */
  --violet-50:  #EDE9FE;
  --violet-100: #DDD6FE;
  --violet-200: #C4B5FD;
  --violet-300: #A78BFA;
  --violet-400: #8B5CF6;
  --violet-500: #7C4DFF;
  --violet-600: #6C3AED;  /* PRIMÁRNA */
  --violet-700: #5B21B6;
  --violet-800: #4C1D95;
  --violet-900: #3B0764;

  /* Neutral — Ink */
  --ink-50:  #F8F7FF;  /* Ghost */
  --ink-100: #F0EFF5;
  --ink-200: #E2E1EA;
  --ink-300: #C8C7D4;
  --ink-400: #9E9DAE;
  --ink-500: #6E6D80;
  --ink-600: #4A4958;
  --ink-700: #2D2C3E;
  --ink-800: #1A1A2E;  /* INK — primárny text */
  --ink-900: #0F0F12;  /* Deep — dark mode bg */

  /* Backgrounds */
  --snow:  #FAFAFA;
  --ghost: #F8F7FF;
  --white: #FFFFFF;
}
```

### Akcentové / Sémantické farby

```css
:root {
  /* Success — Green */
  --success-50:  #ECFDF5;
  --success-100: #D1FAE5;
  --success-400: #34D399;
  --success-500: #10B981;
  --success-600: #059669;
  --success-700: #047857;

  /* Warning — Amber */
  --warning-50:  #FFFBEB;
  --warning-100: #FEF3C7;
  --warning-400: #FBBF24;
  --warning-500: #F59E0B;
  --warning-600: #D97706;
  --warning-700: #B45309;

  /* Danger — Red */
  --danger-50:  #FEF2F2;
  --danger-100: #FEE2E2;
  --danger-400: #F87171;
  --danger-500: #EF4444;
  --danger-600: #DC2626;
  --danger-700: #B91C1C;

  /* Info — Blue */
  --info-50:  #EFF6FF;
  --info-100: #DBEAFE;
  --info-400: #60A5FA;
  --info-500: #3B82F6;
  --info-600: #2563EB;
  --info-700: #1D4ED8;
}
```

### Tailwind config mapovanie

```js
// tailwind.config.ts
module.exports = {
  theme: {
    extend: {
      colors: {
        violet: {
          50:  '#EDE9FE',
          100: '#DDD6FE',
          200: '#C4B5FD',
          300: '#A78BFA',
          400: '#8B5CF6',
          500: '#7C4DFF',
          600: '#6C3AED',
          700: '#5B21B6',
          800: '#4C1D95',
          900: '#3B0764',
        },
        ink: {
          50:  '#F8F7FF',
          100: '#F0EFF5',
          200: '#E2E1EA',
          300: '#C8C7D4',
          400: '#9E9DAE',
          500: '#6E6D80',
          600: '#4A4958',
          700: '#2D2C3E',
          800: '#1A1A2E',
          900: '#0F0F12',
        },
        success: {
          50: '#ECFDF5', 100: '#D1FAE5', 400: '#34D399',
          500: '#10B981', 600: '#059669', 700: '#047857',
        },
        warning: {
          50: '#FFFBEB', 100: '#FEF3C7', 400: '#FBBF24',
          500: '#F59E0B', 600: '#D97706', 700: '#B45309',
        },
        danger: {
          50: '#FEF2F2', 100: '#FEE2E2', 400: '#F87171',
          500: '#EF4444', 600: '#DC2626', 700: '#B91C1C',
        },
        info: {
          50: '#EFF6FF', 100: '#DBEAFE', 400: '#60A5FA',
          500: '#3B82F6', 600: '#2563EB', 700: '#1D4ED8',
        },
      },
    },
  },
};
```

### Kedy čo použiť

| Farba | Použitie |
|---|---|
| `violet-600` | CTA tlačidlá, primárne akcie, aktívne tab stavy, linky, focus ringy, progress bary |
| `violet-500` | Hover stav primárnych tlačidiel, sekundárne zvýraznenia |
| `violet-50` | Hover pozadie riadkov, selected stavy, badge/chip pozadia, jemné highlights |
| `ink-800` | Hlavné nadpisy, body text (light mode) |
| `ink-600` | Sekundárny text, labely |
| `ink-400` | Placeholder text, disabled stavy, hint text |
| `ink-50 (ghost)` | Sidebar pozadie, sekcie, card backgrounds (light mode) |
| `ink-900` | Dark mode — hlavné pozadie |
| `ink-700` | Dark mode — card/sidebar pozadie |
| `snow (#FAFAFA)` | Page background (light mode) |
| `white (#FFFFFF)` | Card pozadie, input pozadie (light mode) |

### Gradienty

```css
/* Primárny gradient — CTA tlačidlá, hero sekcie, ikony */
background: linear-gradient(135deg, #6C3AED, #7C4DFF);

/* Jemný gradient — karty, hovery */
background: linear-gradient(135deg, #EDE9FE, #F8F7FF);

/* Dark hero gradient */
background: linear-gradient(135deg, #0F0F12, #1A1A2E);
```

### Dark mode pravidlá

| Element | Light mode | Dark mode |
|---|---|---|
| Page bg | `snow (#FAFAFA)` | `ink-900 (#0F0F12)` |
| Card bg | `white (#FFFFFF)` | `ink-700 (#2D2C3E)` |
| Sidebar bg | `ghost (#F8F7FF)` | `ink-800 (#1A1A2E)` |
| Primary text | `ink-800 (#1A1A2E)` | `#F0EFF5` |
| Secondary text | `ink-600 (#4A4958)` | `ink-400 (#9E9DAE)` |
| Borders | `ink-200 (#E2E1EA)` | `ink-600 (#4A4958)` |
| Primary button | `violet-600` | `violet-500` |
| Input bg | `white` | `ink-800` |

---

## 3. TYPOGRAFIA

### Font stack

```css
/* Headings */
font-family: 'Space Grotesk', -apple-system, BlinkMacSystemFont, sans-serif;

/* Body */
font-family: 'Inter', -apple-system, BlinkMacSystemFont, sans-serif;

/* Code / Mono */
font-family: 'JetBrains Mono', 'Fira Code', monospace;
```

### Google Fonts import

```html
<link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700&family=Space+Grotesk:wght@400;500;600;700&display=swap" rel="stylesheet">
```

### Tailwind config

```js
// tailwind.config.ts
module.exports = {
  theme: {
    extend: {
      fontFamily: {
        heading: ['"Space Grotesk"', 'sans-serif'],
        body: ['"Inter"', 'sans-serif'],
        mono: ['"JetBrains Mono"', '"Fira Code"', 'monospace'],
      },
    },
  },
};
```

### Type scale

| Token | Font | Veľkosť | Line-height | Weight | Letter-spacing | Tailwind |
|---|---|---|---|---|---|---|
| `display` | Space Grotesk | 48px | 56px | 700 | -1px | `font-heading text-5xl font-bold tracking-tight` |
| `h1` | Space Grotesk | 36px | 40px | 700 | -0.5px | `font-heading text-4xl font-bold tracking-tight` |
| `h2` | Space Grotesk | 28px | 34px | 600 | -0.3px | `font-heading text-3xl font-semibold` |
| `h3` | Space Grotesk | 22px | 28px | 600 | 0 | `font-heading text-2xl font-semibold` |
| `h4` | Space Grotesk | 18px | 24px | 600 | 0 | `font-heading text-lg font-semibold` |
| `body-lg` | Inter | 18px | 28px | 400 | 0 | `text-lg` |
| `body` | Inter | 16px | 26px | 400 | 0 | `text-base` |
| `body-sm` | Inter | 14px | 22px | 400 | 0 | `text-sm` |
| `caption` | Inter | 12px | 16px | 500 | 0.2px | `text-xs font-medium tracking-wide` |
| `overline` | Inter | 11px | 16px | 600 | 2px | `text-[11px] font-semibold tracking-[0.15em] uppercase` |

### Pravidlá použitia

- **Space Grotesk**: všetky nadpisy (h1-h4), display text, logo, navigačné titulky, KPI čísla na dashboarde
- **Inter**: body text, labely formulárov, popisy, tabuľky, tlačidlá, inputy, toasty, chybové hlášky
- **Mono**: kódové bloky, embed kódy, tracking ID, API kľúče, technické hodnoty

---

## 4. SPACING & LAYOUT

### Spacing scale (4px grid)

```
4px   → gap medzi inline elementmi, icon padding
8px   → gap medzi label a input, vnútorné medzery malých komponentov
12px  → padding v badges, pills, malé card padding
16px  → štandardný gap medzi elementmi, input padding horizontal
20px  → card padding compact
24px  → card padding default, gap medzi sekciami
32px  → gap medzi kartami, section padding
40px  → oddeľovanie väčších sekcií
48px  → veľké sekcie
64px  → page section spacing
80px  → hero section top/bottom padding
```

### Layout breakpoints

```js
// tailwind.config.ts
screens: {
  'sm':  '640px',   // Mobile landscape
  'md':  '768px',   // Tablet
  'lg':  '1024px',  // Desktop (sidebar collapse breakpoint)
  'xl':  '1280px',  // Wide desktop
  '2xl': '1536px',  // Ultra-wide
}
```

### Admin dashboard layout

```
┌─────────────────────────────────────────────────┐
│ Topbar (h: 56px, bg: white/ink-800)             │
├──────────┬──────────────────────────────────────┤
│ Sidebar  │ Main content                         │
│ w: 260px │ padding: 24px                        │
│ bg: ghost│ max-width: 1280px                    │
│          │ margin: 0 auto                       │
│ collapse │                                      │
│ @ <1024  │                                      │
│ → 0px    │                                      │
└──────────┴──────────────────────────────────────┘
```

- Sidebar: 260px, collapsible na mobile (sheet/drawer)
- Topbar: 56px výška, sticky
- Main content: max-width 1280px, padding 24px
- Page title + breadcrumbs vždy hore

---

## 5. BORDER RADIUS

| Token | Hodnota | Použitie |
|---|---|---|
| `radius-sm` | 4px | Inline badge, tag, tooltip arrow |
| `radius-md` | 6px | Inputy, selecty, textareas |
| `radius-lg` | 10px | Tlačidlá, dropdown menu |
| `radius-xl` | 12px | Karty, modaly, dialógy |
| `radius-2xl` | 16px | Hero karty, veľké panely |
| `radius-full` | 9999px | Avatary, badge pills, toggle switches |

### Tailwind mapovanie

```js
borderRadius: {
  'sm':   '4px',
  'md':   '6px',
  'lg':   '10px',
  'xl':   '12px',
  '2xl':  '16px',
  'full': '9999px',
}
```

---

## 6. SHADOWS

```css
/* Používame minimalistické tiene — žiadne agresívne drop shadows */

--shadow-sm:  0 1px 2px rgba(0, 0, 0, 0.04);
--shadow-md:  0 2px 8px rgba(0, 0, 0, 0.06);
--shadow-lg:  0 4px 16px rgba(0, 0, 0, 0.08);
--shadow-xl:  0 8px 32px rgba(0, 0, 0, 0.10);

/* Violet glow — pre CTA hover stavy */
--shadow-violet: 0 4px 20px rgba(108, 58, 237, 0.25);

/* Focus ring */
--ring-violet: 0 0 0 3px rgba(108, 58, 237, 0.3);
```

| Token | Použitie |
|---|---|
| `shadow-sm` | Inputy, selecty |
| `shadow-md` | Karty, dropdown |
| `shadow-lg` | Modaly, dialog |
| `shadow-xl` | Toast, floating elements |
| `shadow-violet` | CTA hover, primárne tlačidlá hover |
| `ring-violet` | Focus state na interaktívnych elementoch |

---

## 7. KOMPONENTY — Dizajn pravidlá

### Tlačidlá

```
Primary:
  bg: violet-600 | hover: violet-500 | active: violet-700
  text: white | font: Inter 600 14px
  padding: 10px 24px | radius: 10px
  shadow hover: shadow-violet
  transition: all 150ms ease

Secondary (outline):
  bg: transparent | border: 2px violet-600
  text: violet-600 | hover bg: violet-50
  padding: 10px 24px | radius: 10px

Ghost:
  bg: transparent | border: 1px ink-200
  text: ink-600 | hover bg: ink-50
  padding: 10px 24px | radius: 10px

Danger:
  bg: danger-500 | hover: danger-600
  text: white
  padding: 10px 24px | radius: 10px

Veľkosti:
  sm: padding 6px 16px, font 13px, radius 8px
  md: padding 10px 24px, font 14px, radius 10px (default)
  lg: padding 12px 32px, font 16px, radius 12px
```

### Inputy & Formuláre

```
Input:
  bg: white (dark: ink-800)
  border: 1px ink-200 (dark: ink-600)
  focus border: violet-600 + ring-violet
  radius: 6px
  padding: 10px 14px
  font: Inter 400 14px
  placeholder: ink-400

Label:
  font: Inter 500 14px
  color: ink-800 (dark: ink-100)
  margin-bottom: 6px

Error state:
  border: danger-500
  ring: 0 0 0 3px rgba(239, 68, 68, 0.15)
  error message: danger-600, Inter 400 13px, margin-top 4px

Disabled:
  bg: ink-100 (dark: ink-700)
  opacity: 0.6
  cursor: not-allowed
```

### Karty

```
Card:
  bg: white (dark: ink-700)
  border: 1px ink-200 (dark: ink-600)
  radius: 12px
  padding: 24px
  shadow: shadow-sm (hover: shadow-md)

Card header:
  font: Space Grotesk 600 18px
  color: ink-800
  margin-bottom: 16px
  optional: border-bottom 1px ink-200, padding-bottom 16px
```

### Tabuľky

```
Header row:
  bg: ink-50 (dark: ink-800)
  font: Inter 600 12px uppercase tracking-wide
  color: ink-500
  padding: 12px 16px

Body row:
  bg: white (dark: ink-700)
  border-bottom: 1px ink-100 (dark: ink-600)
  padding: 12px 16px
  font: Inter 400 14px
  hover bg: violet-50 (dark: ink-600)

Selected row:
  bg: violet-50 (dark: violet-900/20)
  border-left: 3px violet-600
```

### Sidebar navigácia

```
Nav item:
  padding: 10px 16px
  radius: 8px
  font: Inter 500 14px
  color: ink-500 (dark: ink-400)
  hover bg: ink-100 (dark: ink-700)

Active nav item:
  bg: violet-50 (dark: violet-900/20)
  color: violet-600
  font-weight: 600
  icon: violet-600

Section label:
  font: Inter 600 11px uppercase tracking-wide
  color: ink-400
  padding: 8px 16px
  margin-top: 16px
```

### Toasty / Notifikácie

```
Success: bg: success-50, border-left: 4px success-500, icon: success-600
Warning: bg: warning-50, border-left: 4px warning-500, icon: warning-600
Danger:  bg: danger-50,  border-left: 4px danger-500,  icon: danger-600
Info:    bg: info-50,    border-left: 4px info-500,    icon: info-600

Text: ink-800 (dark: príslušná 100 odtieň)
Radius: 10px
Padding: 14px 16px
Shadow: shadow-md
Auto-dismiss: 5 sekúnd
Position: top-right, stacked
```

### Badges / Pills

```
Variant štýl: soft (bg: farba-50, text: farba-700, border: none)

Status badges:
  Active:     bg: success-50,  text: success-700
  Paused:     bg: warning-50,  text: warning-700
  Draft:      bg: ink-100,     text: ink-600
  Archived:   bg: ink-100,     text: ink-400
  Error:      bg: danger-50,   text: danger-700

Font: Inter 500 12px
Padding: 4px 10px
Radius: full (9999px)
```

### Modaly / Dialógy

```
Overlay: rgba(0, 0, 0, 0.5) (dark: rgba(0, 0, 0, 0.7))
Modal:
  bg: white (dark: ink-700)
  radius: 16px
  shadow: shadow-xl
  padding: 24px
  max-width: 480px (sm), 640px (md), 800px (lg)
  animation: fade-in + scale from 95% → 100%, 200ms ease-out

Header: Space Grotesk 600 20px, border-bottom optional
Footer: flex, justify-end, gap 12px, border-top optional
```

---

## 8. IKONKY

- Knižnica: **Lucide React** (`lucide-react`)
- Veľkosť: 18px default, 16px v tabuľkách a malých komponentoch, 24px v navigácii
- Stroke width: 1.75 (default), 2 pre zvýraznené (sidebar active)
- Farba: dedí od textu (currentColor)

---

## 9. ANIMÁCIE & TRANSITIONS

```css
/* Štandardné transitions */
--transition-fast:   150ms ease;
--transition-base:   200ms ease;
--transition-slow:   300ms ease;

/* Použitie */
transition: all var(--transition-fast);     /* hover stavy, farby */
transition: all var(--transition-base);     /* otváranie/zatváranie, scale */
transition: transform var(--transition-slow); /* page transitions, modaly */
```

### Pravidlá
- Každý interaktívny element musí mať transition (min hover farba)
- Tlačidlá: `transform: translateY(-1px)` na hover
- Karty: shadow transition na hover
- Modaly: fade + scale (95% → 100%)
- Skeleton loading: pulse animácia `background: ink-100 → ink-200`
- Žiadne bounce, elastic, alebo iné agresívne easing funkcie
- Rešpektuj `prefers-reduced-motion: reduce` — vypni animácie

---

## 10. VEREJNÉ STRÁNKY (Registration, Thank You, Webinar Room)

Tieto stránky vidí koncoví používatelia (registranti na webinár). Dizajn je čistý, dôveryhodný, konverzne orientovaný.

```
Registration page:
  - Biely/svetlý bg, vycentrovaný formulár (max-width 480px)
  - Nadpis webinára: Space Grotesk 700, h1
  - Dátum/čas: badge so success farbou
  - Formulár: meno, email, voliteľne telefón
  - CTA tlačidlo: plná šírka, primárny gradient, veľké (lg)
  - Social proof pod formulárom (počet registrovaných)

Thank you page:
  - Zelený success indikátor hore
  - Odpočítavanie: veľké čísla (Space Grotesk 700 48px)
  - "Pridať do kalendára" tlačidlo
  - Pripomienka o emaili/SMS

Webinar room:
  - Tmavý bg (ink-900) — kino efekt
  - Video prehrávač: max-width 960px, vycentrovaný
  - Chat panel: 320px šírka, vpravo na desktop, pod videom na mobile
  - CTA bar: fixed bottom, violet gradient, biely text
```

---

## 11. ZHRNUTIE PRAVIDIEL

1. **Konzistencia** — nikdy nemixuj farby mimo definovanej palety
2. **Kontrast** — minimálne WCAG AA (4.5:1 pre text, 3:1 pre veľký text)
3. **Whitespace** — radšej viac než menej, vzdušný dizajn
4. **Hierarchia** — Space Grotesk na nadpisy, Inter na všetko ostatné, nikdy naopak
5. **Dark mode** — každý komponent musí fungovať v oboch módoch
6. **Mobile first** — najprv mobile layout, potom rozširovať
7. **Feedback** — každá akcia musí mať vizuálnu odozvu (hover, active, focus, loading, success, error)
8. **Slovenčina** — všetky UI texty po slovensky, kód a komentáre po anglicky
9. **Minimalizmus** — žiadne zbytočné dekorácie, tiene, gradienty mimo definovaných
10. **Prístupnosť** — focus visible stavy, aria labels, keyboard navigácia

---

*Tento súbor je živý dokument. Aktualizuj ho keď pridáš nové komponenty alebo zmeníš existujúce pravidlá.*
