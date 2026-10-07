<?php
// Signal main layout to load editor.js
$includeEditor = true;

$noteId      = isset($note['id'])         ? (int)$note['id']        : null;
$noteTitle   = $note                      ? htmlspecialchars($note['title'])   : '';
$noteContent = $note                      ? $note['content']         : '';
$updatedAt   = $note
    ? date('M j, Y \a\t g:i A', strtotime($note['updated_at']))
    : null;
?>
<div class="editor-wrapper" id="editorWrapper">

    <!-- ── Toolbar ───────────────────────────────────────────────────────── -->
    <div class="editor-toolbar" id="editorToolbar" role="toolbar" aria-label="Text formatting toolbar">

        <div class="toolbar-scroll-area">
        <!-- Mode switcher pills -->
        <div class="mode-switch-group" role="radiogroup" aria-label="Editor Mode">
            <button type="button" class="mode-pill active" id="btnModeRich" data-mode="rich" aria-pressed="true" title="Rich Text Mode (WYSIWYG)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" aria-hidden="true">
                    <path d="M4 7V4h16v3M9 20h6M12 4v16"/>
                </svg>
                <span>Rich Text</span>
            </button>
            <button type="button" class="mode-pill" id="btnModeMd" data-mode="markdown" aria-pressed="false" title="Markdown Mode (GFM)">
                <svg width="13" height="13" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.3" aria-hidden="true">
                    <rect x="2" y="4" width="20" height="16" rx="2"/>
                    <path d="M6 15V9l3 3 3-3v6M18 12l-2-2.5v5M18 12l2-2.5v5"/>
                </svg>
                <span>Markdown</span>
            </button>
        </div>

        <div class="toolbar-separator" aria-hidden="true"></div>

        <!-- ── Rich Text Toolbar Group ───────────────────────────────── -->
        <div class="toolbar-group" id="richToolbarGroup">
            <!-- Bold -->
            <button class="toolbar-btn" id="tbBold" data-cmd="bold" data-tooltip="Bold (Ctrl+B)" aria-label="Bold" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                    <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                </svg>
            </button>

            <!-- Italic -->
            <button class="toolbar-btn" id="tbItalic" data-cmd="italic" data-tooltip="Italic (Ctrl+I)" aria-label="Italic" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="19" y1="4" x2="10" y2="4"></line>
                    <line x1="14" y1="20" x2="5" y2="20"></line>
                    <line x1="15" y1="4" x2="9" y2="20"></line>
                </svg>
            </button>

            <!-- Underline -->
            <button class="toolbar-btn" id="tbUnderline" data-cmd="underline" data-tooltip="Underline (Ctrl+U)" aria-label="Underline" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M6 3v7a6 6 0 0 0 6 6 6 6 0 0 0 6-6V3"></path>
                    <line x1="4" y1="21" x2="20" y2="21"></line>
                </svg>
            </button>

            <!-- Strikethrough -->
            <button class="toolbar-btn" id="tbStrike" data-cmd="strikeThrough" data-tooltip="Strikethrough" aria-label="Strikethrough" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <path d="M16 6C16 6 14.5 4 12 4s-4 1-4 3 1.5 3 4 3"></path>
                    <path d="M8 18c0 0 1.5 2 4 2s4-1 4-3-1.5-3-4-3"></path>
                </svg>
            </button>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- Heading level -->
            <select class="toolbar-select" id="tbHeading" aria-label="Text style / heading level">
                <option value="">Paragraph</option>
                <option value="h1">Heading 1</option>
                <option value="h2">Heading 2</option>
                <option value="h3">Heading 3</option>
            </select>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- Bullet list -->
            <button class="toolbar-btn" id="tbUl" data-cmd="insertUnorderedList" data-tooltip="Bullet list" aria-label="Bullet list" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <line x1="9" y1="6"  x2="20" y2="6"></line>
                    <line x1="9" y1="12" x2="20" y2="12"></line>
                    <line x1="9" y1="18" x2="20" y2="18"></line>
                    <circle cx="4" cy="6"  r="1.5" fill="currentColor" stroke="none"></circle>
                    <circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="none"></circle>
                    <circle cx="4" cy="18" r="1.5" fill="currentColor" stroke="none"></circle>
                </svg>
            </button>

            <!-- Numbered list -->
            <button class="toolbar-btn" id="tbOl" data-cmd="insertOrderedList" data-tooltip="Numbered list" aria-label="Numbered list" aria-pressed="false">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <line x1="10" y1="6"  x2="21" y2="6"></line>
                    <line x1="10" y1="12" x2="21" y2="12"></line>
                    <line x1="10" y1="18" x2="21" y2="18"></line>
                    <path d="M4 6h1v4" stroke-width="1.8"></path>
                    <path d="M4 10H6" stroke-width="1.8"></path>
                    <path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1" stroke-width="1.8"></path>
                </svg>
            </button>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- Text color picker -->
            <div class="color-picker-wrapper toolbar-btn" title="Text color" data-tooltip="Text color" role="button" aria-label="Choose text color">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M9 3L5 21M19 3l-4 18M5 9h14M3 15h18"></path>
                </svg>
                <span class="color-indicator" id="colorIndicator" aria-hidden="true"></span>
                <input type="color" class="color-picker-input" id="tbColor" value="#000000" aria-label="Pick a text color">
            </div>
        </div><!-- /#richToolbarGroup -->

        <!-- ── Markdown Toolbar Group (shown when in MD mode) ────────── -->
        <div class="toolbar-group" id="mdToolbarGroup" style="display:none">
            <!-- View Mode Switch (Write | Split | Preview) -->
            <div class="md-view-switch" role="radiogroup" aria-label="Markdown view mode">
                <button type="button" class="md-view-btn" id="mdViewWrite" data-view="write" title="Editor only">Write</button>
                <button type="button" class="md-view-btn active" id="mdViewSplit" data-view="split" title="Side-by-side live preview">Split</button>
                <button type="button" class="md-view-btn" id="mdViewPreview" data-view="preview" title="Rendered preview only">Preview</button>
            </div>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- MD Bold -->
            <button class="toolbar-btn" id="mdBold" data-tooltip="Bold (**text**)" aria-label="Bold">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <path d="M6 4h8a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                    <path d="M6 12h9a4 4 0 0 1 4 4 4 4 0 0 1-4 4H6z"></path>
                </svg>
            </button>

            <!-- MD Italic -->
            <button class="toolbar-btn" id="mdItalic" data-tooltip="Italic (*text*)" aria-label="Italic">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="19" y1="4" x2="10" y2="4"></line>
                    <line x1="14" y1="20" x2="5" y2="20"></line>
                    <line x1="15" y1="4" x2="9" y2="20"></line>
                </svg>
            </button>

            <!-- MD Strikethrough -->
            <button class="toolbar-btn" id="mdStrike" data-tooltip="Strikethrough (~~text~~)" aria-label="Strikethrough">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="5" y1="12" x2="19" y2="12"></line>
                    <path d="M16 6C16 6 14.5 4 12 4s-4 1-4 3 1.5 3 4 3"></path>
                    <path d="M8 18c0 0 1.5 2 4 2s4-1 4-3-1.5-3-4-3"></path>
                </svg>
            </button>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- MD Headings -->
            <select class="toolbar-select" id="mdHeading" aria-label="Markdown heading">
                <option value="">Heading</option>
                <option value="# ">H1 (#)</option>
                <option value="## ">H2 (##)</option>
                <option value="### ">H3 (###)</option>
                <option value="#### ">H4 (####)</option>
            </select>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- MD Code Inline -->
            <button class="toolbar-btn" id="mdCode" data-tooltip="Inline Code (`code`)" aria-label="Inline Code">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.2" aria-hidden="true">
                    <polyline points="16 18 22 12 16 6"></polyline>
                    <polyline points="8 6 2 12 8 18"></polyline>
                </svg>
            </button>

            <!-- MD Code Block -->
            <button class="toolbar-btn" id="mdCodeBlock" data-tooltip="Code Block (```)" aria-label="Code Block">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <polyline points="8 10 6 12 8 14"></polyline>
                    <polyline points="16 10 18 12 16 14"></polyline>
                    <line x1="13" y1="9" x2="11" y2="15"></line>
                </svg>
            </button>

            <!-- MD Quote -->
            <button class="toolbar-btn" id="mdQuote" data-tooltip="Quote (> quote)" aria-label="Blockquote">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M3 21c3 0 7-1 7-8V5c0-1.25-.756-2.017-2-2H4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path>
                    <path d="M15 21c3 0 7-1 7-8V5c0-1.25-.757-2.017-2-2h-4c-1.25 0-2 .75-2 1.972V11c0 1.25.75 2 2 2 1 0 1 0 1 1v1c0 1-1 2-2 2s-1 .008-1 1.031V20c0 1 0 1 1 1z"></path>
                </svg>
            </button>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- MD Bullet List -->
            <button class="toolbar-btn" id="mdUl" data-tooltip="Bullet List (- item)" aria-label="Bullet list">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <line x1="9" y1="6"  x2="20" y2="6"></line>
                    <line x1="9" y1="12" x2="20" y2="12"></line>
                    <line x1="9" y1="18" x2="20" y2="18"></line>
                    <circle cx="4" cy="6"  r="1.5" fill="currentColor" stroke="none"></circle>
                    <circle cx="4" cy="12" r="1.5" fill="currentColor" stroke="none"></circle>
                    <circle cx="4" cy="18" r="1.5" fill="currentColor" stroke="none"></circle>
                </svg>
            </button>

            <!-- MD Numbered List -->
            <button class="toolbar-btn" id="mdOl" data-tooltip="Numbered List (1. item)" aria-label="Numbered list">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <line x1="10" y1="6"  x2="21" y2="6"></line>
                    <line x1="10" y1="12" x2="21" y2="12"></line>
                    <line x1="10" y1="18" x2="21" y2="18"></line>
                    <path d="M4 6h1v4" stroke-width="1.8"></path>
                    <path d="M4 10H6" stroke-width="1.8"></path>
                    <path d="M6 18H4c0-1 2-2 2-3s-1-1.5-2-1" stroke-width="1.8"></path>
                </svg>
            </button>

            <!-- MD Task / Checklist -->
            <button class="toolbar-btn" id="mdTask" data-tooltip="Checklist (- [ ] task)" aria-label="Task list">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polyline points="9 11 12 14 22 4"></polyline>
                    <path d="M21 12v7a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2V5a2 2 0 0 1 2-2h11"></path>
                </svg>
            </button>

            <div class="toolbar-separator" aria-hidden="true"></div>

            <!-- MD Link -->
            <button class="toolbar-btn" id="mdLink" data-tooltip="Link ([text](url))" aria-label="Insert link">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <path d="M10 13a5 5 0 0 0 7.54.54l3-3a5 5 0 0 0-7.07-7.07l-1.72 1.71"></path>
                    <path d="M14 11a5 5 0 0 0-7.54-.54l-3 3a5 5 0 0 0 7.07 7.07l1.71-1.71"></path>
                </svg>
            </button>

            <!-- MD Table -->
            <button class="toolbar-btn" id="mdTable" data-tooltip="Insert Markdown Table" aria-label="Insert table">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <rect x="3" y="3" width="18" height="18" rx="2"></rect>
                    <line x1="3" y1="9" x2="21" y2="9"></line>
                    <line x1="3" y1="15" x2="21" y2="15"></line>
                    <line x1="9" y1="3" x2="9" y2="21"></line>
                    <line x1="15" y1="3" x2="15" y2="21"></line>
                </svg>
            </button>

            <!-- MD Horizontal Rule -->
            <button class="toolbar-btn" id="mdHr" data-tooltip="Divider (---)" aria-label="Horizontal rule">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2.5" aria-hidden="true">
                    <line x1="3" y1="12" x2="21" y2="12"></line>
                </svg>
            </button>
        </div><!-- /#mdToolbarGroup -->

        </div><!-- /.toolbar-scroll-area -->

        <div class="toolbar-end-actions">
            <!-- Download dropdown -->
            <div class="dropdown-wrapper">
                <button class="toolbar-btn" id="downloadBtn" data-tooltip="Download note" aria-label="Download note" aria-haspopup="true" aria-expanded="false">
                    <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                        <path d="M21 15v4a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2v-4"></path>
                        <polyline points="7 10 12 15 17 10"></polyline>
                        <line x1="12" y1="15" x2="12" y2="3"></line>
                    </svg>
                </button>
                <div class="dropdown-menu" id="downloadMenu" role="menu">
                    <a class="dropdown-item" id="dlMd" href="#" role="menuitem">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" aria-hidden="true">
                            <rect x="2" y="4" width="20" height="16" rx="2"></rect>
                            <path d="M6 14V10l2 2 2-2v4M16 11l-1.5-1.5v4.5M16 11l1.5-1.5v4.5"></path>
                        </svg>
                        Markdown (.md)
                    </a>
                    <a class="dropdown-item" id="dlTxt" href="#" role="menuitem">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" aria-hidden="true">
                            <path d="M14 2H6a2 2 0 0 0-2 2v16a2 2 0 0 0 2 2h12a2 2 0 0 0 2-2V8z"></path>
                            <polyline points="14 2 14 8 20 8"></polyline>
                        </svg>
                        Plain text (.txt)
                    </a>
                    <a class="dropdown-item" id="dlHtml" href="#" role="menuitem">
                        <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="15" height="15" aria-hidden="true">
                            <polyline points="16 18 22 12 16 6"></polyline>
                            <polyline points="8 6 2 12 8 18"></polyline>
                        </svg>
                        HTML file (.html)
                    </a>
                </div>
            </div>

            <!-- History link (only on existing notes) -->
            <?php if ($noteId): ?>
            <a class="toolbar-btn" href="<?= APP_URL ?>/note/history/<?= $noteId ?>" data-tooltip="View change history" aria-label="View change history" title="Change history">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <polyline points="1 4 1 10 7 10"></polyline>
                    <path d="M3.51 15a9 9 0 1 0 .49-4.51"></path>
                </svg>
            </a>
            <?php endif; ?>
        </div><!-- /.toolbar-end-actions -->

    </div><!-- /.editor-toolbar -->

    <!-- ── Editor body ────────────────────────────────────────────────────── -->
    <div class="editor-body">

        <!-- Note title -->
        <input
            type="text"
            id="noteTitle"
            class="editor-title-input"
            placeholder="Note title…"
            value="<?= $noteTitle ?>"
            maxlength="255"
            aria-label="Note title"
            spellcheck="true"
        >

        <!-- Meta (last updated) -->
        <?php if ($updatedAt): ?>
        <div class="editor-meta">
            <span>
                <svg width="12" height="12" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" aria-hidden="true">
                    <circle cx="12" cy="12" r="10"></circle>
                    <polyline points="12 6 12 12 16 14"></polyline>
                </svg>
                Last updated: <?= $updatedAt ?>
            </span>
        </div>
        <?php endif; ?>

        <div class="editor-separator" aria-hidden="true"></div>

        <!-- Content (rich text) -->
        <div
            id="noteContent"
            class="editor-content"
            contenteditable="true"
            data-placeholder="Start writing your note…"
            aria-label="Note content"
            aria-multiline="true"
            role="textbox"
            spellcheck="true"
        ><?= $noteContent ?></div>

        <!-- Content (Markdown workspace: editor + live preview) -->
        <div id="markdownWorkspace" class="markdown-workspace view-split" style="display:none">
            <div class="markdown-pane md-editor-pane" id="mdEditorPane">
                <textarea
                    id="noteMarkdown"
                    class="markdown-textarea"
                    placeholder="# Start writing in Markdown...&#10;&#10;Supports **bold**, *italic*, lists, tables, and code blocks."
                    aria-label="Markdown content"
                    spellcheck="true"
                ></textarea>
            </div>
            <div class="markdown-pane md-preview-pane" id="mdPreviewPane">
                <div id="markdownPreview" class="markdown-preview markdown-body" aria-label="Markdown live preview"></div>
            </div>
        </div>

    </div><!-- /.editor-body -->

    <!-- ── Footer: word count + delete + Xeon AI ────────────────────── -->
    <div class="editor-footer">
        <div class="editor-footer-left">
            <span class="editor-stat" id="wordCount" aria-live="polite">0 words</span>
            <span class="editor-stat" id="charCount" aria-live="polite">0 chars</span>
        </div>

        <div class="editor-footer-center">
            <button type="button" class="btn-xeon-ai-footer" id="xeonAiBtn" title="Open Xeon AI Assistant">
                <span class="material-symbols-outlined ai-sparkle-spin" aria-hidden="true">auto_awesome</span>
                <span>Xeon AI</span>
            </button>
        </div>

        <div class="editor-footer-right">
            <?php if ($noteId): ?>
            <button class="btn-delete-note" id="deleteNoteBtn" data-note-id="<?= $noteId ?>" aria-label="Delete this note">
                <svg viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" width="14" height="14" aria-hidden="true">
                    <polyline points="3 6 5 6 21 6"></polyline>
                    <path d="M19 6v14a2 2 0 0 1-2 2H7a2 2 0 0 1-2-2V6m3 0V4a1 1 0 0 1 1-1h4a1 1 0 0 1 1 1v2"></path>
                </svg>
                Delete note
            </button>
            <?php endif; ?>
        </div>
    </div>

</div><!-- /.editor-wrapper -->

<!-- ── Xeon AI Assistant Modal (Editor) ───────────────────────────── -->
<div class="modal-overlay" id="xeonAiModal" role="dialog" aria-modal="true" aria-labelledby="xeonAiModalTitle">
    <div class="modal modal-lg ai-modal-card">
        <!-- Header -->
        <div class="ai-modal-header">
            <div class="ai-modal-header-title">
                <span class="material-symbols-outlined ai-sparkle-icon" aria-hidden="true">auto_awesome</span>
                <div>
                    <h3 id="xeonAiModalTitle" class="ai-modal-heading">Xeon AI Assistant</h3>
                    <span style="font-size:12px; color:var(--text-muted);">Intelligent note writing & transformation</span>
                </div>
            </div>
            <button type="button" class="btn-icon" id="closeXeonAiModal" aria-label="Close modal">
                <svg width="18" height="18" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2">
                    <line x1="18" y1="6" x2="6" y2="18"></line>
                    <line x1="6" y1="6" x2="18" y2="18"></line>
                </svg>
            </button>
        </div>

        <!-- Mode navigation tabs -->
        <div class="ai-nav-tabs" role="tablist">
            <button type="button" class="ai-tab-btn active" data-tab="prompt" role="tab" aria-selected="true">
                <span class="material-symbols-outlined text-sm">edit</span>
                <span>Prompt / Generate</span>
            </button>
            <button type="button" class="ai-tab-btn" data-tab="templates" role="tab" aria-selected="false">
                <span class="material-symbols-outlined text-sm">space_dashboard</span>
                <span>Templates</span>
            </button>
            <button type="button" class="ai-tab-btn" data-tab="transform" role="tab" aria-selected="false">
                <span class="material-symbols-outlined text-sm">transform</span>
                <span>Transform Note</span>
            </button>
        </div>

        <!-- Body -->
        <div class="ai-modal-body">

            <!-- TAB 1: Prompt / Generate -->
            <div class="ai-tab-panel active" id="aiTabPrompt">
                <div class="form-group">
                    <label class="form-label" for="aiPromptText">What should Xeon AI write or continue?</label>
                    <textarea
                        id="aiPromptText"
                        class="form-input"
                        rows="4"
                        placeholder="E.g., Explain microservices architectural patterns with pros and cons, or draft meeting minutes..."
                        style="resize:vertical;"
                    ></textarea>
                </div>

                <div class="form-group">
                    <label class="form-label">Quick Ideas</label>
                    <div class="ai-quick-suggestions">
                        <button type="button" class="suggestion-chip" data-prompt="Continue writing the next logical paragraphs based on this note.">Continue writing</button>
                        <button type="button" class="suggestion-chip" data-prompt="Extract and list all key actionable takeaways from this note.">Extract action items</button>
                        <button type="button" class="suggestion-chip" data-prompt="Create a clear study quiz with 5 questions and answers based on this text.">Create 5-question quiz</button>
                        <button type="button" class="suggestion-chip" data-prompt="Explain this concept simply using an intuitive real-world analogy.">Explain with analogy</button>
                    </div>
                </div>
            </div>

            <!-- TAB 2: Templates -->
            <div class="ai-tab-panel" id="aiTabTemplates">
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">Select a structured document template to generate:</p>
                <div class="ai-template-grid">
                    <div class="template-select-card" data-template="meeting_notes">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">groups</span>
                            <strong>Meeting Notes</strong>
                        </div>
                        <p>Agenda, attendees, key decisions, and action items checklist.</p>
                    </div>
                    <div class="template-select-card" data-template="study_summary">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">school</span>
                            <strong>Study Summary</strong>
                        </div>
                        <p>Core concepts, definitions, deep breakdown, and review points.</p>
                    </div>
                    <div class="template-select-card" data-template="todo_list">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">checklist</span>
                            <strong>Action Checklist</strong>
                        </div>
                        <p>Prioritized task breakdown with `- [ ]` markdown checkboxes.</p>
                    </div>
                    <div class="template-select-card" data-template="project_plan">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">account_tree</span>
                            <strong>Project Plan</strong>
                        </div>
                        <p>Objectives, milestone phases, timeline, and risk mitigation.</p>
                    </div>
                    <div class="template-select-card" data-template="technical_doc">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">terminal</span>
                            <strong>Technical Doc</strong>
                        </div>
                        <p>Architecture, prerequisites, setup, endpoints, and examples.</p>
                    </div>
                    <div class="template-select-card" data-template="brainstorming">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">lightbulb</span>
                            <strong>Brainstorming</strong>
                        </div>
                        <p>Challenge statement, idea clusters, pros/cons, and next steps.</p>
                    </div>
                    <div class="template-select-card" data-template="formal_email">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">mail</span>
                            <strong>Formal Email</strong>
                        </div>
                        <p>Polished business letter with subject, purpose, and clear CTA.</p>
                    </div>
                    <div class="template-select-card" data-template="creative_draft">
                        <div class="template-card-head">
                            <span class="material-symbols-outlined text-primary">article</span>
                            <strong>Article Draft</strong>
                        </div>
                        <p>Engaging headline, hook, structured body, and conclusions.</p>
                    </div>
                </div>

                <div class="form-group" style="margin-top:14px;">
                    <label class="form-label" for="aiTemplateTopic">Specific Topic / Context (Optional)</label>
                    <input type="text" id="aiTemplateTopic" class="form-input" placeholder="e.g. Q3 Sales Strategy Sync, or Quantum Computing basics">
                </div>
            </div>

            <!-- TAB 3: Transform Current Note -->
            <div class="ai-tab-panel" id="aiTabTransform">
                <p style="font-size:13px; color:var(--text-muted); margin-bottom:14px;">Transform or polish the existing text in this note:</p>
                <div class="ai-transform-actions">
                    <button type="button" class="btn-transform" data-action="summarize">
                        <span class="material-symbols-outlined">summarize</span>
                        <div>
                            <strong>Summarize Note</strong>
                            <span>Create a concise bulleted summary of key points</span>
                        </div>
                    </button>
                    <button type="button" class="btn-transform" data-action="expand">
                        <span class="material-symbols-outlined">unfold_more</span>
                        <div>
                            <strong>Expand &amp; Elaborate</strong>
                            <span>Add detailed explanations and practical examples</span>
                        </div>
                    </button>
                    <button type="button" class="btn-transform" data-action="fix_grammar">
                        <span class="material-symbols-outlined">spellcheck</span>
                        <div>
                            <strong>Fix Grammar &amp; Polish</strong>
                            <span>Refine spelling, clarity, and overall flow</span>
                        </div>
                    </button>
                    <button type="button" class="btn-transform" data-action="change_tone">
                        <span class="material-symbols-outlined">tune</span>
                        <div>
                            <strong>Change Writing Tone</strong>
                            <span>Rewrite with the selected tone profile below</span>
                        </div>
                    </button>
                </div>
            </div>

            <!-- Shared Options: Tone & Target -->
            <div class="ai-shared-options">
                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label">Voice / Tone</label>
                    <div class="ai-tone-chips" id="editorToneChips" role="radiogroup">
                        <button type="button" class="tone-chip active" data-tone="balanced">Balanced</button>
                        <button type="button" class="tone-chip" data-tone="professional">Professional</button>
                        <button type="button" class="tone-chip" data-tone="casual">Casual</button>
                        <button type="button" class="tone-chip" data-tone="academic">Academic</button>
                        <button type="button" class="tone-chip" data-tone="concise">Concise</button>
                    </div>
                </div>

                <div class="form-group" style="margin-bottom:0;">
                    <label class="form-label" for="aiInsertMode">Destination</label>
                    <select id="aiInsertMode" class="form-input">
                        <option value="cursor">Insert at Cursor</option>
                        <option value="append">Append to End of Note</option>
                        <option value="replace">Replace Entire Note</option>
                    </select>
                </div>
            </div>

            <!-- Generation Loading Box -->
            <div class="ai-loading-box" id="editorAiLoading" style="display:none;">
                <div class="ai-loading-spinner"></div>
                <div class="ai-loading-status" id="editorAiStatusText">Xeon AI is generating your content...</div>
            </div>

            <!-- Generated Output Preview Box -->
            <div class="ai-preview-box" id="editorAiPreviewBox" style="display:none;">
                <div class="ai-preview-header">
                    <span class="ai-preview-title" id="editorAiPreviewTitle">Generated Output Preview</span>
                    <div style="display:flex; align-items:center; gap:8px;">
                        <button type="button" class="btn-skip-typing" id="editorAiSkipTypingBtn" style="display:none;" title="Skip typing animation">
                            <span class="material-symbols-outlined text-xs">fast_forward</span>
                            <span>Skip</span>
                        </button>
                        <button type="button" class="btn btn-ghost" id="copyAiOutputBtn" style="padding:4px 10px; font-size:12px;">
                            <span class="material-symbols-outlined text-xs">content_copy</span>
                            <span>Copy</span>
                        </button>
                    </div>
                </div>
                <div class="ai-preview-content" id="editorAiPreviewContent"></div>
            </div>

        </div>

        <!-- Footer Modal Actions -->
        <div class="modal-actions ai-modal-actions">
            <button type="button" class="btn btn-ghost" id="cancelXeonAiModal">Cancel</button>
            <button type="button" class="btn btn-primary" id="editorAiGenerateBtn">
                <span class="material-symbols-outlined text-sm" aria-hidden="true">auto_awesome</span>
                <span id="editorAiGenerateBtnText">Generate</span>
            </button>
            <button type="button" class="btn btn-success" id="editorAiApplyBtn" style="display:none;">
                <span class="material-symbols-outlined text-sm" aria-hidden="true">check</span>
                <span>Insert into Note</span>
            </button>
        </div>
    </div>
</div>


<!-- Expose note data to editor.js -->
<script>
window.XEON = {
    noteId:    <?= $noteId !== null ? (int)$noteId : 'null' ?>,
    appUrl:    '<?= rtrim(APP_URL, '/') ?>',
    csrfToken: '<?= $csrfToken ?>',
};
</script>
