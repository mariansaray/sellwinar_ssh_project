# SELLWINAR — Kompletná špecifikácia projektu

> Tento dokument je jediný zdroj pravdy pre celý projekt Sellwinar. Obsahuje všetko — od vízie, cez funkcie, až po presný postup implementácie. AI agent sa riadi výhradne týmto dokumentom. Ak tu niečo nie je, nerobí sa to.

---

## 0. PRAVIDLÁ KOMUNIKÁCIE S VLASTNÍKOM PROJEKTU

### ⚠️ TOTO JE KRITICKY DÔLEŽITÉ — DODRŽUJ VŽDY

Vlastník projektu (Marián) **nie je programátor**. Komunikuj s ním takto:

1. **Ako s úplným laikom** — nepredpokladaj žiadne technické znalosti. Ani HTML, ani Terminal, ani Git. Vysvetľuj jednoducho.
2. **Ak potrebuješ aby niečo spravil** (napr. zadal príkaz do Terminálu), napíš mu:
   - Čo presne má otvoriť (napr. "Otvor aplikáciu Terminal na Macu — stlač Cmd + Space, napíš Terminal, stlač Enter")
   - Presný príkaz na skopírovanie — formátovaný v code bloku, aby ho len skopíroval a vložil
   - Čo má očakávať (napr. "Uvidíš text 'Done.' — to znamená že to prebehlo úspešne")
   - Čo robiť ak niečo zlyhá
3. **Nikdy nepoužívaj technický žargón** bez vysvetlenia
4. **Ak sa dá niečo automatizovať** (script, príkaz), vždy uprednostni automatizáciu pred manuálnymi krokmi
5. **Všetko čo ide na server** rob cez pripravené scripty — vlastník len skopíruje príkaz a vloží do Terminálu

### Prístupové údaje
Prístupové údaje k serveru, databáze a Git repozitáru sú v súbore **`PRISTUPY.md`** (nie je v Gite — je v .gitignore). AI agent si ich prečíta keď potrebuje pripraviť deploy príkazy alebo konfiguráciu.

### Git repozitár
- SSH: `git@github.com:mariansaray/sellwinar_ssh_project.git`
- HTTPS: `https://github.com/mariansaray/sellwinar_ssh_project.git`

---

## 1. VÍZIA A ÚČEL

### Čo je Sellwinar?
Sellwinar je **SaaS platforma na evergreen webináre a smart video prehrávač** pre marketérov, tvorcov kurzov a podnikateľov. Primárny trh: Slovensko a Česko.

### Kto sú používatelia?

| Rola | Kto to je | Čo vidí |
|---|---|---|
| **Super Admin** | Vlastník platformy (ty) | Administrácia celej platformy — všetci užívatelia, všetky webináre, globálne nastavenia, billing, systémové logy |
| **Užívateľ** | Podnikateľ/marketér, ktorý si platí účet | Vlastný dashboard — správa svojich webinárov, smart videí, divákov, analytiky, emailov, nastavení |
| **Divák** | Koncový človek, ktorý pozerá webinár/video | Registračná stránka, ďakujeme stránka, webinár miestnosť, embeddované video na cudzej stránke |

### Dva hlavné režimy

**Režim 1 — Evergreen webinár**
Kompletný webinárový funnel: registrácia → pripomienky → webinár miestnosť → predaj. Simuluje živý webinár, ale všetko je predtočené a automatizované.

**Režim 2 — Smart video (embed)**
Inteligentný video prehrávač s konverznými prvkami. Vygeneruje sa embed kód, vloží sa na akúkoľvek stránku. Žiadna registrácia, žiadny scheduling. Funguje ako marketingový nástroj na predaj.

---

## 2. TECHNOLÓGIA

### Stack

| Vrstva | Technológia |
|---|---|
| Backend | **Laravel** (PHP 8.1+) |
| Databáza | MariaDB / MySQL 8 |
| Frontend CSS | Tailwind CSS (CDN alebo kompilovaný) |
| Frontend JS | Alpine.js (CDN) |
| Grafy | Chart.js (CDN) |
| Platby | Stripe (cez Laravel Cashier alebo priame API) |
| E-mail | **Laravel Mail** (built-in, lepšia integrácia než PHPMailer — podporuje queue, šablóny, driver pre SMTP/Mailgun/SES) |
| SMS | Twilio REST API cez HTTP (Laravel HTTP client) |
| Ikony | Lucide Icons (alebo Heroicons) inline SVG |
| Queue | Laravel Queue s database driverom (žiadny Redis potrebný) |

### Prečo Laravel Mail (nie PHPMailer)?
- Natívna integrácia s Laravel queue → emaily sa posielajú na pozadí
- Blade šablóny pre emaily → čistejší kód
- Built-in podpora pre SMTP, Mailgun, Amazon SES, Postmark
- Automatický retry pri zlyhaniach
- Mail logging out of the box
- Markdown email šablóny

### Deploy workflow (SSH + Git)
Server má SSH prístup. Prístupové údaje sú v `PRISTUPY.md` (nie je v Gite).
Git repozitár: `git@github.com:mariansaray/sellwinar_ssh_project.git`

**Prvý deploy (jednorázovo):**
AI agent pripraví presné príkazy, ktoré vlastník skopíruje do Terminálu.

```bash
# 1. Pripojiť sa na server (vlastník skopíruje do Terminálu)
ssh -p 222 project@sellwinar.com@ssh.sellwinar.com

# 2. Stiahnuť projekt z Gitu
git clone git@github.com:mariansaray/sellwinar_ssh_project.git /var/www/sellwinar

# 3. Nainštalovať závislosti
cd /var/www/sellwinar
composer install --optimize-autoloader --no-dev

# 4. Nastaviť prostredie
cp .env.example .env
# AI agent pripraví .env s predvyplnenými hodnotami z PRISTUPY.md
php artisan key:generate

# 5. Spustiť migrácie a seed
php artisan migrate --force
php artisan db:seed --force

# 6. Nastaviť permissions
chmod -R 775 storage bootstrap/cache
chown -R www-data:www-data storage bootstrap/cache

# 7. Nastaviť cron job pre Laravel Scheduler
crontab -e
# Pridať riadok: * * * * * cd /var/www/sellwinar && php artisan schedule:run >> /dev/null 2>&1

# 8. Spustiť queue worker (na pozadí)
nohup php artisan queue:work database --sleep=3 --tries=3 &

# 9. Nasmerovať webserver (Apache/Nginx) na /var/www/sellwinar/public
```

**Aktualizácie (keď AI agent dopíše nový kód):**
AI agent pripraví príkaz, vlastník ho skopíruje do Terminálu:
```bash
ssh -p 222 project@sellwinar.com@ssh.sellwinar.com "cd /var/www/sellwinar && git pull && composer install --no-dev && php artisan migrate --force"
```

**Deploy script:** V projekte bude `deploy.sh` — AI agent ho pripraví tak, aby vlastník len spustil `bash deploy.sh` a všetko prebehne automaticky. Vlastník nemusí rozumieť čo script robí.

**Deploy script:** V projekte bude pripravený `deploy.sh` — stačí spustiť `bash deploy.sh` a urobí kroky 3-8 automaticky.

**Aktualizácie:** `git pull && composer install --no-dev && php artisan migrate --force` — jeden príkaz.

### Pravidlá
- Žiadny npm/webpack/vite na serveri — frontend knižnice z CDN
- Žiadny Redis, žiadny Node.js na serveri
- Queue driver: `database` (funguje všade, ale s queue workerom cez SSH sa emaily/SMS posielajú okamžite)
- Scheduler: jeden cron job `* * * * * php artisan schedule:run`
- Queue worker: `php artisan queue:work` beží na pozadí (cez supervisor alebo nohup)
- Celá UI v **slovenčine**, kód a komentáre v **angličtine**

---

## 3. ŠTRUKTÚRA ROLÍ A PRÍSTUPOV

### Super Admin (vlastník platformy)

Prihlasuje sa na `domena.sk/super-admin`. Vidí:

- **Dashboard** — celkový prehľad: počet užívateľov, aktívne subscriptions, MRR (mesačný opakovaný príjem), celkový počet webinárov, registrácií
- **Správa užívateľov** — zoznam všetkých užívateľov, ich plán, stav predplatného, počet webinárov. Môže: deaktivovať účet, zmeniť plán, prihlásiť sa "ako užívateľ" (impersonation), resetovať heslo
- **Billing prehľad** — všetky platby, faktúry, MRR graf, churn rate
- **Globálne nastavenia** — Stripe API kľúče, defaultné SMTP, systémový email, doména, údržba mód
- **Systémové logy** — chybové logy, email delivery logy, webhook logy
- **Plány a ceny** — správa billing plánov (mesačný/ročný/lifetime)

### Užívateľ (zákazník platformy)

Prihlasuje sa na `domena.sk/login`. Vidí svoju dashboard sekciu:

- **Dashboard** — KPI karty (aktívne webináre, registrácie dnes/týždeň/mesiac, konverzný pomer), grafy
- **Webináre** — zoznam svojich webinárov, vytvoriť nový, editovať, duplikovať, archivovať
- **Smart videá** — zoznam embeddovateľných smart videí, vytvoriť nové, editovať
- **Diváci** — zoznam všetkých registrantov, filtre, vyhľadávanie, export CSV
- **Analytika** — detailné grafy a metriky pre webináre aj smart videá
- **E-mail šablóny** — správa emailových šablón pre pripomienky
- **SMS šablóny** — správa SMS šablón
- **Nastavenia** — profil, SMTP config, Twilio config, tracking pixely, webhooky, embed formuláre
- **Billing** — aktuálny plán, faktúry, upgrade/downgrade, zrušenie

### Divák (koncový používateľ)

Nemá žiadny účet. Vidí len:
- Registračnú stránku webinára
- Ďakujeme stránku
- Webinár miestnosť (video + chat + CTA)
- Embeddované smart video na cudzej stránke

---

## 4. REŽIM 1 — EVERGREEN WEBINÁR (detaily)

### 4.1 Vytvorenie webinára (admin UI pre Užívateľa)

**Krok 1 — Základné info:**
- Názov webinára
- Interný popis (len pre admina)
- URL slug (auto-generovaný z názvu, editovateľný)
- Status: koncept / aktívny / pozastavený / archivovaný

**Krok 2 — Video:**
- Zdroj videa: YouTube URL, Vimeo URL, alebo vlastná URL (MP4/HLS)
- Prehrávač: YouTube IFrame API (skrytý natívny UI), Vimeo Player SDK, alebo HTML5 video
- Náhľadový obrázok (thumbnail) — upload alebo auto z videa

**Krok 3 — Plánovanie (schedule):**

| Typ | Popis |
|---|---|
| **Just-in-time** | Webinár začne X minút po registrácii (napr. 15 min). Každý divák má svoj vlastný čas. Najvyšší konverzný pomer. |
| **Fixné časy** | Opakujúce sa časy — napr. každý utorok a štvrtok o 19:00. Divák si vyberie z dostupných termínov. |
| **Interval** | Webinár každých X hodín (napr. každé 2 hodiny). Divák vidí najbližší voľný čas. |

- Timezone: automatická detekcia timezone diváka + manuálne nastavenie default timezone
- Skrytie nočných termínov (napr. nezobrazovať časy medzi 23:00-7:00)

**Krok 4 — Registračná stránka:**

**Editor/Builder registračnej stránky:**
- **5 predpripravených šablón** — užívateľ si vyberie jednu a upraví ju
  - Šablóna 1: Minimalistická (veľký nadpis + formulár)
  - Šablóna 2: S obrázkom/videom vľavo, formulár vpravo
  - Šablóna 3: S bullet points benefitmi a countdown časovačom
  - Šablóna 4: Dlhšia stránka s testimonialmi a FAQ
  - Šablóna 5: Urgency štýl (limitované miesta, odpočítavanie)
- **Editovateľné prvky:**
  - Nadpis a podnadpis webinára
  - Popis / bullet body benefits
  - Obrázok alebo video preview
  - Farby (primárna, pozadie, text)
  - Logo
  - CTA text tlačidla registrácie
  - Custom CSS (pre pokročilých)
- **Formulárové polia:**
  - Meno (povinné/voliteľné)
  - E-mail (vždy povinné)
  - Telefón (voliteľné — pre SMS pripomienky)
  - Custom polia (max 3, text input)
- **Social proof:** voliteľne zobraziť "Už X ľudí sa registrovalo"
- **Countdown timer:** odpočítavanie do najbližšieho termínu

**Krok 5 — Ďakujeme stránka (Thank You):**
- Zelený success indikátor
- Odpočítavanie do začiatku webinára (veľké čísla)
- "Pridať do Google Calendar" tlačidlo (generovaný gcal link)
- "Pridať do iCal" tlačidlo (.ics súbor)
- Pripomienka: "Pošleme ti email X minút pred začiatkom"
- Voliteľne: CTA na medzičas (napr. "Pozri si toto video kým čakáš")

**Krok 6 — Webinár miestnosť:**
- Tmavé pozadie (kino efekt)
- Video prehrávač (viď sekcia 6 — Player)
- Chat panel (viď sekcia 7 — Chat)
- CTA tlačidlo pod videom (viď sekcia 8 — CTA)
- Fejkové purchase alerty (viď sekcia 9 — Alerty)
- Počet divákov badge: "🔴 LIVE • X ľudí sleduje" (fejkový, nastaviteľný rozsah min-max, mení sa každých 15-45 sekúnd)

**Krok 7 — E-mail pripomienky:**
- Potvrdenie registrácie (ihneď)
- Pripomienka 24h pred
- Pripomienka 1h pred
- Pripomienka 15 min pred
- Pripomienka 5 min pred (s priamym linkom)
- Zmeškaný webinár (ak sa nezúčastnil)
- Replay link (voliteľne)
- Každú šablónu možno zapnúť/vypnúť
- Placeholder systém: `{{meno}}`, `{{email}}`, `{{datum}}`, `{{cas}}`, `{{link}}`, `{{link_na_zrusenie}}`

**Krok 8 — SMS pripomienky:**
- Rovnaké triggery ako email
- Max 160 znakov na správu
- Vyžaduje aby divák zadal telefón pri registrácii

**Krok 9 — Tracking pixely:**
- Facebook Pixel ID + eventy (PageView, Lead, ViewContent, Purchase)
- Google Analytics (GA4) Measurement ID
- Google Ads Conversion ID
- TikTok Pixel
- Custom script (ľubovoľný JavaScript)
- Nastaviteľné per stránka: registrácia, ďakujeme, webinár miestnosť

**Krok 10 — Analytika webinára:**
- Funnel vizualizácia: Návštevy reg. stránky → Registrácie → Účasť → CTA klik → Konverzia
- Konverzné pomery medzi krokmi
- Priemerná doba sledovania
- Drop-off body (kedy diváci odchádzajú)
- Registrácie podľa dňa/týždňa
- UTM zdroje registrácií
- Export CSV

---

## 5. REŽIM 2 — SMART VIDEO / EMBED (detaily)

### 5.1 Vytvorenie smart videa (admin UI pre Užívateľa)

**Základné info:**
- Názov (interný, pre admina)
- Zdroj videa: YouTube URL, Vimeo URL, vlastná URL
- Thumbnail obrázok — upload alebo auto

**Player nastavenia:**
- Farba prehrávača (primárna farba progress baru, tlačidiel)
- Pozadie prehrávača
- Zobrazenie/skrytie ovládacích prvkov
- **Fake progress bar** — ukazuje kratší čas než reálne video (napr. 60 min video vyzerá ako 30 min). Divák si myslí, že "ešte chvíľku" a pozerá dlhšie
- **Zákaz pretáčania** — divák nemôže preskakovať dopredu (dozadu voliteľne áno)
- **Smart autoplay** — video sa spustí automaticky MUTED. Keď divák klikne kamkoľvek na prehrávač, zapne sa zvuk. Funguje vo všetkých prehliadačoch.
- **Thumbnail pred prehraním** — veľký play button na custom obrázku
- **Rýchlosť prehrávania** — možnosť zapnúť/vypnúť ovládanie rýchlosti (0.5x–2x)

**CTA tlačidlo:**
- Text tlačidla
- URL kam smeruje
- Čas zobrazenia (v sekundách od začiatku videa)
- Čas zmiznutia (voliteľné — ak prázdne, zostáva natrvalo)
- Farba tlačidla
- Pozícia: pod videom

**Fejkové purchase alerty:**
- Viď sekcia 9

**Analytika smart videa:**
- Počet načítaní (loads) — koľkokrát sa prehrávač zobrazil na stránke
- Počet spustení (plays) — koľkokrát niekto klikol play
- Priemerná doba pozerania
- Percentuálne míľniky: koľko divákov sa dostalo na 25% / 50% / 75% / 100%
- Engagement heatmap — vizualizácia kde diváci pretáčajú, pausujú, odchádzajú
- CTA kliknutia — koľko, kedy
- Konverzný pomer: CTA kliky / plays
- Unikátni vs. celkoví diváci
- Zariadenie (desktop / mobil / tablet)
- Zdroj návštevnosti (referrer URL)

**Embed kód:**
- Systém vygeneruje `<script>` snippet + `<div>` kontajner
- Responsívny — prispôsobí sa šírke kontajnera
- Domain restriction — voliteľne obmedziť na ktorých doménach embed funguje
- Ukážka: admin vidí live preview embed kódu priamo v dashboarde

---

## 6. VIDEO PREHRÁVAČ (spoločný pre oba režimy)

### Technické riešenie
- **YouTube**: YouTube IFrame API — skryje sa natívny UI cez CSS overlay div, custom controls cez JavaScript
- **Vimeo**: Vimeo Player SDK (CDN) — embed bez natívnych controls
- **Custom URL**: HTML5 `<video>` element

### Custom controls (JavaScript + Alpine.js)
- Play / Pause tlačidlo
- Progress bar (s fake variantou pre smart video)
- Hlasitosť + mute/unmute
- Fullscreen
- Aktuálny čas / celkový čas
- Loading spinner pri bufferovaní

### Player config (JSON v databáze)
```json
{
  "primaryColor": "#6C3AED",
  "backgroundColor": "#000000",
  "showPlayPause": true,
  "showProgress": true,
  "allowSeeking": false,
  "fakeProgressBar": false,
  "fakeDurationSeconds": null,
  "showVolume": true,
  "showFullscreen": true,
  "showSpeed": false,
  "autoplay": true,
  "startMuted": true,
  "thumbnailUrl": null
}
```

### Tracking (JavaScript → API)
- Každých 10 sekúnd odošle `video_progress` event na API
- Eventy: `video_load`, `video_play`, `video_pause`, `video_resume`, `video_complete`, `video_progress`, `cta_click`
- Údaje: session_id, registrant_id (ak webinár), seconds_watched, total_duration, percentage

---

## 7. CHAT SYSTÉM (len Režim 1 — Webinár)

### Zapnutie / Vypnutie
- Každý webinár má toggle: chat zapnutý / vypnutý
- Ak vypnutý, chat panel sa vôbec nezobrazí

### Vrstva 1 — Fejkové správy (predpripravené)
Admin vytvorí chat šablónu pre webinár:

| Pole | Popis |
|---|---|
| Meno odosielateľa | Napr. "Andrea K." |
| Avatar | Automaticky generovaný z iniciálok (farebný krúžok) |
| Text správy | "Super, toto som presne potrebovala!" |
| Typ | správa / otázka / reakcia / systémová |
| Zobraziť v čase | 125 (sekúnd od začiatku videa) |

- Správy sa zobrazujú synchronizovane s videom — ak video pauzneme, chat sa zastaví
- Bulk import z CSV (stĺpce: meno, text, sekunda, typ)
- Admin si vie naklikať desiatky až stovky správ pre realistický efekt

### Vrstva 2 — Reálne správy od divákov
- Divák vidí input pole v chate a môže napísať správu
- Správa sa odošle na server a uloží do databázy
- Správa sa **nezobrazí** ostatným divákom (každý divák vidí len fejkové správy + svoje vlastné + odpovede admina pre neho)

### Vrstva 3 — Kontrolná miestnosť (admin)
- Samostatná stránka v admin dashboarde: "Kontrolná miestnosť" pre konkrétny webinár
- Admin vidí **v reálnom čase** (polling každé 3 sekundy) všetky reálne správy od divákov
- Pri každej správe vidí: meno diváka, email, čas, text správy
- Admin môže kliknúť "Odpovedať" → napíše odpoveď → tá sa zobrazí **iba tomu konkrétnemu divákovi** v jeho chate
- Badge s počtom neprečítaných správ v sidebar navigácii

### Technické riešenie
- JavaScript polling každé 3 sekundy: `GET /api/chat?webinar_id=X&session_id=Y&since=TIMESTAMP`
- Server vráti: nové fejkové správy pre aktuálny čas videa + nové odpovede admina pre tohto diváka
- Žiadny WebSocket, žiadny Pusher — čistý polling cez fetch()

---

## 8. CTA TLAČIDLO (oba režimy)

### Konfigurácia (admin UI)
- **Text tlačidla** — napr. "Chcem sa prihlásiť" / "Kúpiť teraz"
- **URL** — kam tlačidlo smeruje
- **Čas zobrazenia** — v sekundách od začiatku videa (napr. 1800 = 30 minút)
- **Čas zmiznutia** — voliteľné (ak prázdne, zostáva natrvalo)
- **Farba tlačidla** — color picker
- **Farba textu** — color picker
- **Animácia pri zobrazení** — jemný fade-in + scale efekt

### Zobrazenie
- Tlačidlo sa zobrazí **pod videom** (nie overlay na videu)
- Plná šírka prehrávača
- Veľké, výrazné — hlavný konverzný prvok
- Na mobile: sticky na spodku obrazovky (voliteľné)

### Tracking
- Event `cta_show` keď sa tlačidlo zobrazí
- Event `cta_click` keď divák klikne
- Ukladá sa: session_id, registrant_id, timestamp, video_second

---

## 9. FEJKOVÉ PURCHASE ALERTY (oba režimy)

### Konfigurácia (admin UI)
Admin vytvorí zoznam alertov:

| Pole | Popis |
|---|---|
| Meno | "Andrea K." |
| Produkt | "Kurz XYZ" alebo "Premium balík" |
| Zobraziť v čase | 1850 (sekúnd od začiatku videa) |

- Text šablóna: `"{{meno}} si práve zakúpil/a {{produkt}}"` — formát nastaviteľný
- Možnosť randomizovať poradie (voliteľné)
- Import z CSV

### Zobrazenie
- Toast/popup notifikácia — vľavo dole alebo vpravo dole
- Ikona nákupného košíka alebo checkmark
- Animácia: slide-in, zostane 5 sekúnd, slide-out
- Zvukový efekt: voliteľné jemné "ding" (zapnúť/vypnúť v nastaveniach)

---

## 10. REGISTRÁCIA DIVÁKOV A WEBHOOKY

### Registračný flow
1. Divák príde na registračnú stránku (`/w/{slug}`)
2. Vyplní formulár (meno, email, príp. telefón, custom polia)
3. UTM parametre sa automaticky zachytia z URL
4. Systém vytvorí záznam v DB, vygeneruje unikátny `access_token`
5. Podľa scheduling typu priradí `scheduled_at`
6. Odošle potvrdzovací email
7. **Odošle webhook** (ak je nastavený)
8. Presmeruje na ďakujeme stránku

### Embeddovateľný registračný formulár
- Admin si v dashboarde vygeneruje embed kód pre konkrétny webinár
- **Varianty:**
  - `<script>` snippet — vloží iframe s formulárom, automaticky responsívny
  - `<iframe>` — priamy iframe
  - Standalone URL — link na registračnú stránku
- Formulár funguje na akejkoľvek cudzej stránke
- Po registrácii: redirect na thank you page ALEBO zobrazenie success správy v iframe (nastaviteľné)
- CORS headers správne nastavené

### Webhooky (VEĽMI DÔLEŽITÉ)

**Odchádzajúce webhooky (Sellwinar → externý systém):**
Užívateľ nastaví webhook URL a vyberie eventy:

| Event | Kedy sa odošle | Payload príklad |
|---|---|---|
| `registration.created` | Nová registrácia | `{event, registrant: {name, email, phone, webinar, scheduled_at, utm_*}}` |
| `registration.attended` | Divák sa pripojil na webinár | `{event, registrant, joined_at}` |
| `registration.missed` | Divák sa nezúčastnil | `{event, registrant, scheduled_at}` |
| `registration.cta_clicked` | Divák klikol na CTA | `{event, registrant, cta_url, video_second}` |

- HMAC-SHA256 podpis v headeri `X-Sellwinar-Signature`
- Retry logika: 3 pokusy (ihneď, po 5 min, po 30 min)
- Webhook log: admin vidí históriu odoslaných webhookov, status, response

**Prichádzajúce webhooky (externý systém → Sellwinar):**
- Endpoint: `POST /api/webhook/incoming/{webhook_token}`
- Použitie: externý systém môže vytvoriť registráciu cez API
- Autentifikácia: unikátny webhook token pre každého užívateľa
- Akcie: vytvoriť registráciu, aktualizovať status registrácie

### API pre integrácie
- `POST /api/v1/registrants` — vytvoriť registráciu
- `GET /api/v1/registrants` — zoznam registrácií
- `GET /api/v1/registrants/{id}` — detail registrácie
- `GET /api/v1/webinars` — zoznam webinárov
- API key autentifikácia (každý užívateľ má svoj API kľúč v nastaveniach)
- Rate limiting: 100 requestov/minútu

---

## 11. E-MAIL SYSTÉM

### SMTP konfigurácia (per užívateľ)
- Užívateľ zadá svoje SMTP údaje: host, port, user, heslo, from name, from email
- Heslo šifrované v DB (AES-256)
- Tlačidlo "Odoslať testovací email" — overí spojenie
- Alternatíva: Sellwinar môže poskytnúť defaultný SMTP (Super Admin nastaví globálny)

### Email šablóny
- Predpripravené defaultné šablóny pre každý trigger (užívateľ ich len upraví)
- WYSIWYG editor pre HTML emaily (jednoduchý — bold, italic, link, obrázok, farba)
- Placeholder systém: `{{meno}}`, `{{email}}`, `{{datum_webinara}}`, `{{cas_webinara}}`, `{{link_na_webinar}}`, `{{link_na_zrusenie}}`, `{{nazov_webinara}}`
- Preview: admin vidí ako bude email vyzerať pred uložením

### Odosielanie
- Laravel Queue (database driver) — emaily sa posielajú na pozadí
- Laravel Scheduler: `php artisan schedule:run` každú minútu (jeden cron job)
- Scheduler kontroluje registrantov × email šablóny → ak `scheduled_at - delay_minutes = now` → zaradí email do queue
- Notification log: každý email má záznam (queued, sent, failed)
- Rate limiting podľa plánu

---

## 12. SMS SYSTÉM

### Twilio konfigurácia (per užívateľ)
- Account SID, Auth Token (šifrovaný), Phone Number
- Test SMS tlačidlo

### SMS šablóny
- Max 160 znakov
- Rovnaké triggery ako email (potvrdenie, pripomienky, zmeškaný)
- Placeholder systém rovnaký ako email

### Odosielanie
- Laravel HTTP client → Twilio REST API
- Cez queue na pozadí
- Delivery status cez Twilio webhook
- Notification log

---

## 13. ANALYTIKA

### Dashboard užívateľa — prehľadové KPI karty
- Aktívne webináre (počet)
- Aktívne smart videá (počet)
- Registrácie dnes / tento týždeň / tento mesiac
- Konverzný pomer (registrácia → CTA klik)
- Celkový čas sledovania

### Analytika per webinár
- **Funnel:** Návštevy reg. stránky → Registrácie → Účasť → CTA klik
- Konverzné pomery medzi krokmi
- Registrácie po dňoch (line chart)
- Priemerná doba sledovania
- Drop-off body (kde diváci odchádzajú)
- UTM zdroje (odkiaľ prichádzajú registrácie)
- Zariadenia (desktop/mobil/tablet)
- Export CSV

### Analytika per smart video
- Loads / Plays / Priemerná doba / Míľniky (25%/50%/75%/100%)
- CTA kliknutia a konverzný pomer
- Engagement heatmap
- Referrer URLs (z akých stránok embed)
- Zariadenia
- Unikátni vs. celkoví diváci
- Export CSV

### Super Admin analytika
- Celkový počet užívateľov, webinárov, smart videí, registrácií
- MRR (Monthly Recurring Revenue) graf
- Nové registrácie užívateľov po dňoch
- Churn rate
- Top užívatelia podľa počtu registrácií

---

## 14. TRACKING PIXELY

### Podporované platformy
- Facebook Pixel (Meta Pixel)
- Google Analytics 4 (GA4)
- Google Ads Conversion
- TikTok Pixel
- Custom JavaScript snippet

### Nastavenie (per webinár / smart video)
- Admin zadá Pixel ID / Measurement ID
- Vyberie na ktorých stránkach sa má pixel odpaľovať
- Automatické eventy: PageView, Lead (pri registrácii), ViewContent (webinár room), Purchase (pri konverzii)
- Custom eventy: admin môže definovať vlastné

---

## 15. BILLING

### Plány

| Plán | Cena | Limity |
|---|---|---|
| **Mesačný** | 39 €/mesiac | Neobmedzené webináre, neobmedzené smart videá, neobmedzené registrácie |
| **Ročný** | 390 €/rok (32,50 €/mes) | Rovnaké ako mesačný |
| **Lifetime** | 1 170 € jednorazovo | Rovnaké, navždy |

### Trial
- 14 dní zadarmo po registrácii
- Plná funkčnosť
- Po 14 dňoch: paywall — musí si vybrať plán

### Stripe integrácia
- Stripe Checkout pre platby
- Stripe Customer Portal pre správu predplatného
- Stripe Webhooky: `checkout.session.completed`, `invoice.payment_succeeded`, `invoice.payment_failed`, `customer.subscription.deleted`
- Grace period 3 dni pri neúspešnej platbe

### Registrácia nového užívateľa
1. Vyplní formulár: meno, email, heslo, názov firmy/projektu
2. Overí email (klik na link)
3. Aktivuje sa 14-dňový trial
4. Po trialu → redirect na výber plánu → Stripe Checkout
5. Po úspešnej platbe → plný prístup

---

## 16. BEZPEČNOSŤ

### Autentifikácia
- Laravel built-in auth (bcrypt hashing)
- Session-based + "zapamätať ma" cookie
- CSRF token na každom formulári (Laravel auto)
- Session regenerácia po prihlásení
- Impersonation: Super Admin sa môže prihlásiť ako akýkoľvek užívateľ (s vizuálnym indikátorom)

### Tenant izolácia
- Každý Eloquent model má `tenant_id`
- Global scope na modeli: automaticky pridáva `WHERE tenant_id = ?` ku každému query
- Middleware kontroluje `tenant_id` na každom requeste

### Šifrovanie
- SMTP heslá, API tokeny, Twilio credentials: šifrované v DB (Laravel Crypt)
- Webhook secrets: hashed

### Validácia
- Laravel Form Request validácia na každom inpute
- Sanitizácia výstupov (Blade auto-escaping)
- Rate limiting na API endpoints a login

### Prístupové tokeny
- Registrant access token: 64-znakový random hash, platný max 4 hodiny po `scheduled_at`
- API kľúče: 40-znakový random hash, obnoviteľné

---

## 17. DATABÁZOVÉ TABUĽKY (Laravel Migrations)

### Tenants & Users
```
tenants: id, name, slug, plan, stripe_customer_id, stripe_subscription_id, subscription_status, trial_ends_at, settings (JSON), created_at, updated_at

users: id, tenant_id, email, password, name, role (super_admin/owner/admin/editor), email_verified_at, last_login_at, api_key, created_at, updated_at
```

### Webináre & Smart videá
```
webinars: id, tenant_id, name, slug, type (evergreen/smart_video), video_source (youtube/vimeo/custom), video_url, video_duration_seconds, thumbnail_url, player_config (JSON), schedule_config (JSON), registration_page_config (JSON), thankyou_page_config (JSON), chat_enabled, cta_config (JSON), status (draft/active/paused/archived), created_at, updated_at

webinar_schedules: id, webinar_id, tenant_id, schedule_type, fixed_times (JSON), jit_delay_minutes, interval_hours, timezone, hide_night_times, created_at, updated_at
```

### Registrácie
```
registrants: id, tenant_id, webinar_id, email, first_name, last_name, phone, utm_source, utm_medium, utm_campaign, utm_term, utm_content, custom_fields (JSON), scheduled_at, access_token, status (registered/attended/missed/converted), registration_ip, user_agent, created_at, updated_at
```

### Chat
```
chat_messages_fake: id, webinar_id, tenant_id, sender_name, message_text, display_at_seconds, message_type, sort_order, created_at

chat_messages_real: id, webinar_id, tenant_id, registrant_id, session_id, sender_name, message_text, is_admin_reply, reply_to_registrant_id, is_read_by_admin, created_at

chat_config: id, webinar_id, tenant_id, viewer_count_min, viewer_count_max, created_at, updated_at
```

### Purchase alerty
```
purchase_alerts: id, webinar_id, tenant_id, buyer_name, product_name, display_at_seconds, sort_order, created_at, updated_at
```

### Tracking & Analytics
```
tracking_pixels: id, tenant_id, webinar_id, pixel_type, pixel_id, page_placement (JSON array), custom_events (JSON), is_active, created_at, updated_at

analytics_events: id, tenant_id, webinar_id, registrant_id, session_id, event_type, event_data (JSON), ip_address, user_agent, referrer_url, device_type, created_at
```

### Email & SMS
```
email_configs: id, tenant_id, smtp_host, smtp_port, smtp_user, smtp_pass_encrypted, from_name, from_email, reply_to, is_verified, created_at, updated_at

email_templates: id, tenant_id, webinar_id, trigger_type, subject, body_html, is_active, delay_minutes, created_at, updated_at

sms_configs: id, tenant_id, twilio_sid, twilio_token_encrypted, twilio_phone, is_active, created_at, updated_at

sms_templates: id, tenant_id, webinar_id, trigger_type, message_text, is_active, delay_minutes, created_at, updated_at

notification_log: id, tenant_id, registrant_id, channel (email/sms), template_id, status (queued/sent/delivered/failed/bounced), error_message, sent_at, created_at
```

### Webhooky
```
webhooks: id, tenant_id, event_types (JSON array), url, secret, is_active, last_triggered_at, created_at, updated_at

webhook_log: id, webhook_id, tenant_id, event_type, payload (JSON), response_code, response_body, attempt, status, created_at

incoming_webhook_tokens: id, tenant_id, token, is_active, created_at
```

### Embed formuláre
```
embed_forms: id, tenant_id, webinar_id, form_config (JSON), domain_restrictions (JSON), created_at, updated_at
```

### Billing
```
billing_plans: id, name, slug, stripe_price_id, price, interval (monthly/yearly/lifetime), max_webinars, max_registrants, features (JSON), is_active, created_at

billing_history: id, tenant_id, stripe_invoice_id, amount, currency, status, period_start, period_end, created_at
```

---

## 18. UI / BRAND (odkaz na brand code)

Kompletný brand code je v súbore `sellwinar_brand_code.md`. Kľúčové body:
- **Názov**: Sellwinar (Sell = violet 600, winar = ink)
- **Tagline**: "Webináre, ktoré predávajú. Na autopilote."
- **Farby**: Violet (#6C3AED) primárna, Ink (#1A1A2E) text
- **Fonty**: Space Grotesk (nadpisy), Inter (text)
- **Dark mode**: povinný pre všetky komponenty
- **Ikony**: Lucide React
- **Mobile first** prístup

---

## 19. TO-DO (odložené nápady — neimplementovať teraz)

1. **Facebook Ads integrácia** — OAuth, sync dát o utrácaní, ROI dashboard, CPA/ROAS výpočty
2. **A/B testovanie registračných stránok** — 2 varianty, automatické rozdeľovanie návštevnosti
3. **Replay funkcia** — automatické sprístupnenie záznamu po webinári
4. **Ankety/Polls počas webinára** — interaktívne hlasovanie
5. **Multi-jazyková podpora** — UI v rôznych jazykoch
6. **Tímové účty** — užívateľ môže pozvať ďalších členov tímu
7. **Vlastná doména** — užívateľ si môže pripojiť vlastnú doménu pre registračné stránky
8. **Drag & Drop builder** — pokročilý vizuálny editor registračných stránok (zatiaľ 5 šablón)

---

## 20. IMPLEMENTAČNÝ PLÁN — PRESNÝ POSTUP

> Tento zoznam je navrhnutý tak, aby AI agent mohol postupovať príkazom "pokračuj" bez ďalších otázok. Každý krok je jasne definovaný, má vstupy a výstupy.

---

### FÁZA 1 — Základ projektu
**Úloha 1.1:** Inicializácia Laravel projektu
- `laravel new sellwinar`
- Nastaviť `.env.example` so všetkými potrebnými premennými
- Nastaviť `config/app.php`: timezone `Europe/Bratislava`, locale `sk`
- Pridať Tailwind CSS (CDN link v hlavnom layoute)
- Pridať Alpine.js (CDN link v hlavnom layoute)
- Pridať Chart.js (CDN link)
- Pridať Lucide Icons

**Úloha 1.2:** Databázové migrácie
- Vytvoriť všetky migrácie podľa sekcie 17
- Seedery: defaultné billing plány, Super Admin účet

**Úloha 1.3:** Základná konfigurácia
- Laravel Mail config (SMTP driver)
- Queue config (database driver)
- Session config (database driver)
- Vytvorenie `install` route — spustí migrácie cez web, vytvorí Super Admin

---

### FÁZA 2 — Autentifikácia a role
**Úloha 2.1:** Registrácia nového užívateľa
- Registračný formulár: meno, email, heslo, názov firmy
- Email verifikácia
- Vytvorenie tenant záznamu
- Aktivácia 14-dňového trialu
- Redirect na dashboard

**Úloha 2.2:** Login / Logout
- Login formulár (email + heslo)
- "Zapamätať ma" funkcia
- Session management
- Logout

**Úloha 2.3:** Role a middleware
- `SuperAdminMiddleware` — kontroluje role `super_admin`
- `TenantMiddleware` — nastaví aktuálny tenant, pridá global scope
- `SubscriptionMiddleware` — kontroluje aktívne predplatné / trial
- Route groups: `/super-admin/*`, `/dashboard/*`, verejné routes

**Úloha 2.4:** Impersonation
- Super Admin môže kliknúť "Prihlásiť sa ako" pri akomkoľvek užívateľovi
- Vizuálny banner hore: "Ste prihlásený ako XY — Vrátiť sa"

---

### FÁZA 3 — Layout a navigácia
**Úloha 3.1:** Admin layout (pre Užívateľa)
- Sidebar navigácia (260px, collapsible na mobile)
- Topbar (56px, sticky) — meno užívateľa, notifikácie, dark mode toggle
- Main content area (max-width 1280px)
- Breadcrumbs
- Flash messages (success, error, warning)
- Responsívny dizajn (mobile drawer pre sidebar)

**Úloha 3.2:** Super Admin layout
- Oddelený layout s vlastným sidebar
- Navigácia: Dashboard, Užívatelia, Billing, Nastavenia, Logy

**Úloha 3.3:** Dark mode
- Toggle v topbar, uložený v localStorage
- CSS class `dark` na `<html>` elemente
- Všetky komponenty musia fungovať v oboch módoch

---

### FÁZA 4 — Webinár CRUD
**Úloha 4.1:** Zoznam webinárov
- Tabuľka: názov, typ (evergreen/smart video), status, registrácie, dátum vytvorenia
- Filtre: status, typ
- Akcie: editovať, duplikovať, archivovať, zmazať

**Úloha 4.2:** Vytvorenie webinára (wizard)
- Krok 1: Základné info (názov, typ, slug)
- Krok 2: Video (zdroj, URL, thumbnail)
- Krok 3: Player nastavenia (config JSON)
- Ak typ = evergreen: Krok 4-10 podľa sekcie 4
- Ak typ = smart video: CTA + alerty + embed kód

**Úloha 4.3:** Editácia webinára
- Rovnaké formuláre ako vytvorenie, predvyplnené dátami
- Tab navigácia: Info | Video | Player | Schedule | Reg. stránka | Thank You | Chat | CTA | Alerty | E-maily | SMS | Tracking | Analytika

---

### FÁZA 5 — Video prehrávač
**Úloha 5.1:** JavaScript komponent prehrávača
- Alpine.js komponent `x-data="videoPlayer(config)"`
- YouTube IFrame API integrácia
- Vimeo Player SDK integrácia
- HTML5 video element integrácia
- Custom controls: play/pause, progress, volume, fullscreen
- Skrytie natívnych YouTube/Vimeo controls cez CSS overlay

**Úloha 5.2:** Fake progress bar
- Ak `fakeProgressBar: true` → progress bar ukazuje `fakeDurationSeconds` namiesto reálneho trvania
- Čas sa počíta proporcionálne

**Úloha 5.3:** Smart autoplay
- Video sa spustí `muted` + `autoplay`
- Overlay "Klikni pre zvuk" — pri kliknutí sa aktivuje zvuk
- Fallback: ak autoplay nefunguje, zobraziť veľký play button na thumbnailom

**Úloha 5.4:** Zákaz pretáčania
- Ak `allowSeeking: false` → progress bar kliknutie nefunguje
- Divák môže iba pauzovať/spustiť, nie pretáčať dopredu
- Dozadu voliteľne povolené

**Úloha 5.5:** Event tracking
- JavaScript posiela eventy na `/api/track` každých 10 sekúnd
- Eventy: load, play, pause, resume, complete, progress, cta_click, cta_show

---

### FÁZA 6 — Registračný flow
**Úloha 6.1:** Registračná stránka
- Route: `/{tenant-slug}/w/{webinar-slug}` (alebo custom domain v budúcnosti)
- 5 šablón — Blade view s dynamickým obsahom
- Admin si vyberie šablónu a nastaví texty, farby, logo
- Formulár: meno, email, telefón (voliteľné), custom polia
- UTM zachytávanie z URL parametrov
- AJAX submit → API endpoint

**Úloha 6.2:** API registrácia
- `POST /api/register` — vytvorí registranta, vygeneruje access_token
- Scheduling logika (JIT/fixed/interval)
- Odošle potvrdzovací email (cez queue)
- Odošle webhooky (cez queue)
- Odpoveď: redirect URL na thank you page

**Úloha 6.3:** Thank you stránka
- Odpočítavanie do webinára (JavaScript countdown)
- Google Calendar link (generovaný URL)
- iCal súbor (.ics download)
- Tracking pixel odpaľovanie (Lead event)

**Úloha 6.4:** Webinár miestnosť
- Route: `/watch/{access_token}`
- Validácia tokenu (existuje, neexpiroval)
- Ak pred `scheduled_at`: zobraz countdown "Webinár začne o..."
- Ak v okne: zobraz prehrávač + chat + CTA + alerty
- Ak po okne (4h): "Tento webinár už skončil"
- Dark mode layout (kino efekt)

**Úloha 6.5:** Embed registračný formulár
- Generátor embed kódu v admin UI
- Script varianta: `<script src="sellwinar.sk/embed.js?webinar=SLUG"></script>`
- iFrame varianta: `<iframe src="sellwinar.sk/embed/register/SLUG"></iframe>`
- CORS nastavenia
- Post-registration behavior: redirect alebo in-iframe success

---

### FÁZA 7 — Chat systém
**Úloha 7.1:** Fejkové správy — admin UI
- CRUD tabuľka: meno, text, čas (sekundy), typ
- Bulk import CSV
- Náhľad: timeline vizualizácia správ na časovej osi

**Úloha 7.2:** Fejkové správy — frontend
- JavaScript: synchronizácia s video časom
- Polling: `GET /api/chat/fake?webinar_id=X&current_second=Y`
- Zobrazenie v chat paneli

**Úloha 7.3:** Reálne správy — divák
- Input pole v chate pre diváka
- `POST /api/chat/send` → uloží správu do DB
- Správa sa zobrazí len tomu divákovi

**Úloha 7.4:** Kontrolná miestnosť — admin
- Stránka `/dashboard/webinars/{id}/control-room`
- Real-time zoznam správ od divákov (polling 3s)
- "Odpovedať" funkcia → správa sa doručí divákovi
- Badge neprečítaných správ

**Úloha 7.5:** Chat — frontend kompletné
- Kombinuje fejkové správy + vlastné správy diváka + odpovede admina
- Badge "🔴 LIVE • X ľudí sleduje"
- Scroll auto-follow na najnovšiu správu

---

### FÁZA 8 — CTA tlačidlo a purchase alerty
**Úloha 8.1:** CTA tlačidlo — admin konfigurácia
- Formulár: text, URL, čas zobrazenia, čas zmiznutia, farby
- Live preview

**Úloha 8.2:** CTA tlačidlo — frontend
- JavaScript: sleduje video čas, zobrazí/skryje tlačidlo
- Animácia fade-in
- Event tracking pri zobrazení a kliku

**Úloha 8.3:** Purchase alerty — admin konfigurácia
- CRUD tabuľka: meno, produkt, čas
- Import CSV

**Úloha 8.4:** Purchase alerty — frontend
- JavaScript: sleduje video čas, zobrazí toast notifikáciu
- Animácia slide-in / slide-out (5 sekúnd)

---

### FÁZA 9 — E-mail a SMS systém
**Úloha 9.1:** SMTP konfigurácia UI
- Formulár: SMTP údaje
- Test email funkcia
- Šifrovanie hesla

**Úloha 9.2:** Email šablóny UI
- CRUD pre šablóny per webinár
- Defaultné šablóny (auto-vytvorené pri novom webinári)
- WYSIWYG editor (jednoduchý)
- Placeholder systém
- Preview

**Úloha 9.3:** Email scheduler
- Laravel Scheduled Command: každú minútu kontroluje čo treba odoslať
- Porovnáva `registrants.scheduled_at` s `email_templates.delay_minutes`
- Zaradí do queue
- Uloží do notification_log

**Úloha 9.4:** Twilio konfigurácia UI
- Formulár: SID, Auth Token, Phone Number
- Test SMS

**Úloha 9.5:** SMS šablóny UI a scheduler
- Rovnaký princíp ako email
- Laravel HTTP client → Twilio API

---

### FÁZA 10 — Tracking pixely
**Úloha 10.1:** Pixel management UI
- Formulár: typ pixelu, ID, stránky kde odpaľovať
- Custom script textarea

**Úloha 10.2:** Renderovanie pixelov
- Blade component `<x-tracking-pixels :webinar="$webinar" placement="registration" />`
- Automatické odpaľovanie správnych eventov

---

### FÁZA 11 — Analytika
**Úloha 11.1:** Event tracking API
- `POST /api/track` — prijíma JSON, ukladá do `analytics_events`
- Session tracking (cookie-based session_id)

**Úloha 11.2:** Dashboard užívateľa
- KPI karty (Eloquent aggregate queries)
- Chart.js grafy (registrácie za 30 dní, funnel)

**Úloha 11.3:** Analytika per webinár
- Funnel vizualizácia
- Drop-off graf
- UTM tabuľka
- Export CSV

**Úloha 11.4:** Analytika per smart video
- Loads, plays, priemerná doba, míľniky
- Engagement heatmap (zjednodušená verzia)
- CTA konverzie

**Úloha 11.5:** Super Admin analytika
- MRR graf
- Užívatelia po dňoch
- Celkové štatistiky

---

### FÁZA 12 — Webhooky
**Úloha 12.1:** Odchádzajúce webhooky — UI
- CRUD: URL, eventy, secret
- Test webhook (odošle sample payload)
- Log histórie

**Úloha 12.2:** Odchádzajúce webhooky — engine
- Laravel Job: po každom registrant evente odošle webhook
- HMAC podpis
- Retry logika (3x)

**Úloha 12.3:** Prichádzajúce webhooky / API
- REST API endpoints pre registrácie
- Token autentifikácia
- Validácia payloadu
- Rate limiting

---

### FÁZA 13 — Billing (Stripe)
**Úloha 13.1:** Stripe integrácia
- Stripe Checkout Session (pre nový subscription)
- Stripe Customer Portal (pre správu)
- Webhook handler: checkout.completed, invoice.paid, invoice.failed, subscription.deleted

**Úloha 13.2:** Billing UI
- Aktuálny plán, ďalšia platba
- Upgrade/downgrade/cancel
- História faktúr

**Úloha 13.3:** Paywall middleware
- Ak trial expiroval a žiadne aktívne predplatné → redirect na billing stránku
- Grace period 3 dni pri neúspešnej platbe

---

### FÁZA 14 — Super Admin panel
**Úloha 14.1:** Dashboard
- Celkové KPI, MRR, graf nových užívateľov

**Úloha 14.2:** Správa užívateľov
- Zoznam, filtre, vyhľadávanie
- Detail: webináre, registrácie, billing história
- Akcie: deaktivovať, zmeniť plán, impersonation, reset hesla

**Úloha 14.3:** Globálne nastavenia
- Stripe kľúče
- Default SMTP
- Systémový email
- Maintenance mode

**Úloha 14.4:** Systémové logy
- Chybové logy
- Email delivery logy
- Webhook logy

---

### FÁZA 15 — Registrant management
**Úloha 15.1:** Zoznam registrantov (užívateľský dashboard)
- Tabuľka: meno, email, telefón, webinár, dátum, status, UTM
- Filtre: webinár, status, dátum
- Vyhľadávanie
- Pagination (50/stránka)
- Export CSV

**Úloha 15.2:** Detail registranta
- Celá história: registrácia, emaily, SMS, video progress, CTA klik, konverzie
- Chat správy od diváka + odpovede admina
- Editácia údajov

---

### FÁZA 16 — Polish a finalizácia
**Úloha 16.1:** Responsívnosť
- Testovať a opraviť všetky stránky na mobile
- Sidebar drawer na mobile (Alpine.js)

**Úloha 16.2:** Empty states
- Každá tabuľka/zoznam: ak prázdny → priateľská správa + CTA ("Vytvoriť prvý webinár")

**Úloha 16.3:** Loading states
- Spinner/skeleton pri API volaní
- Disabled tlačidlá počas submitu

**Úloha 16.4:** Error handling
- Validačné chyby pod inputmi
- Flash messages pre akcie
- 404, 403, 500 custom stránky

**Úloha 16.5:** Bezpečnostný audit
- CSRF všade
- SQL injection — kontrola (Laravel default OK)
- XSS — Blade auto-escaping
- Tenant izolácia — kontrola global scopes
- Rate limiting na login, API, registráciu

**Úloha 16.6:** Performance
- Eager loading na Eloquent queries (N+1 prevention)
- Indexy na DB tabuľkách
- Cache pre často používané queries (dashboard KPI)

**Úloha 16.7:** Install script
- Route `/install` — spustí migrácie, vytvorí Super Admin
- Po inštalácii: varuje že treba route vypnúť
- Automaticky sa vypne po úspešnej inštalácii

---

*Tento dokument je živý. Aktualizuj ho pri každej zmene požiadaviek.*
