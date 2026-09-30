# 📝 Xeon Notepad

> Modern, Secure, High-Performance Online Notepad with End-to-End Encryption & Dual Mode (WYSIWYG Rich Text & GitHub Flavored Markdown).

![Xeon Notepad](logo/NotepadIcon.png)

---

## ✨ Features

- 🔐 **End-to-End Encryption**: AES-256-GCM encryption with authenticated tags for secure note storage.
- ⚡ **High-Speed Auto-Save**: Real-time debounce auto-saving with instant visual status feedback.
- 🖊️ **Dual Editor Modes**:
  - **Rich Text (WYSIWYG)**: Formatting (Bold, Italic, Underline, Strikethrough, Headings, Bullet & Numbered Lists, Colors).
  - **Markdown (GFM)**: Live side-by-side split preview, write-only mode, or rendered preview with code block highlighting and GitHub-style task lists.
- 📥 **Export Options**: Download notes instantly as Markdown (`.md`), Plain Text (`.txt`), or HTML (`.html`).
- 🕒 **Change History**: Version control for every note, allowing you to review and restore past edits.
- 📱 **100% Mobile Responsive**: Dedicated mobile drawer sidebar, swipeable touch-friendly toolbar, and clean 1-column responsive card grid.
- 🌓 **Dark & Light Mode**: Smooth theme toggling with dark grey and vibrant orange accents.
- 🔍 **Live Search**: Fast instant filter across all your saved notes.

---

## 🚀 Getting Started

### Prerequisites

- PHP 8.0+ with `openssl`, `pdo_mysql`, and `mbstring` extensions enabled.
- MySQL / MariaDB (e.g. via XAMPP, Laragon, or standalone).
- Apache with `mod_rewrite` enabled.

### Installation

1. **Clone the repository**:
   ```bash
   git clone https://github.com/AlfandoXeon/XeonNotepad.git
   ```

2. **Configure environment**:
   Copy `.env.example` to `.env`:
   ```bash
   cp .env.example .env
   ```
   Generate a 32-byte encryption key:
   ```bash
   php -r "echo bin2hex(random_bytes(32));"
   ```
   Paste the generated key into `ENCRYPTION_KEY` in `.env`, and update database credentials.

3. **Import Database**:
   Import `database/xeon_notepad.sql` into MySQL:
   ```bash
   mysql -u root -p xeon_notepad < database/xeon_notepad.sql
   ```

4. **Serve Application**:
   Place the project under your web server root (e.g. `htdocs/XeonNotepad` in XAMPP) and open in your browser:
   ```
   http://localhost/XeonNotepad/
   ```

---

## 🔒 Security

- Passwords hashed with bcrypt (`PASSWORD_BCRYPT`).
- Notes encrypted at rest using `AES-256-GCM` with random 96-bit IV per note/history record.
- CSRF protection enabled on all state-changing endpoints.
- PDO prepared statements with strict typing against SQL injection.

---

## 👤 Author

Developed by **AlfandoXeon**
