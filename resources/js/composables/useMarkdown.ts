import DOMPurify from 'dompurify';
import { marked } from 'marked';
import Prism from 'prismjs';
import 'prismjs/components/prism-bash';
import 'prismjs/components/prism-javascript';
import 'prismjs/components/prism-markup-templating';
import 'prismjs/components/prism-php';
import 'prismjs/components/prism-typescript';
import 'prismjs/components/prism-json';
import 'prismjs/components/prism-sql';
import 'prismjs/components/prism-css';
import 'prismjs/themes/prism-tomorrow.css';
import { nextTick } from 'vue';

// @ts-expect-error - Prism needs to be global for its plugins to work
window.Prism = Prism;

// Helper to safely escape HTML inside code blocks / inline code
function escapeHtml(input: unknown): string {
    const str = String(input ?? '');
    return str
        .replace(/&/g, '&amp;')
        .replace(/</g, '&lt;')
        .replace(/>/g, '&gt;')
        .replace(/"/g, '&quot;')
        .replace(/'/g, '&#39;');
}

// Configure Marked renderer to inject Tailwind-friendly classes
// Using classic renderer API for broad compatibility
const renderer = new marked.Renderer();

// Headings h1-h6
// @ts-expect-error - marked v17 passes token objects; using runtime API via this.parser
renderer.heading = function ({ tokens, depth }: any) {
    const tag = `h${depth}`;
    const cls = `md-h${depth}`;
    const inner = this.parser.parseInline(tokens);
    return `<${tag} class="${cls}">${inner}</${tag}>`;
};

// Paragraphs
// @ts-expect-error - marked v17 passes token objects; using runtime API via this.parser
renderer.paragraph = function ({ tokens }: any) {
    const inner = this.parser.parseInline(tokens);
    return `<p class="md-p">${inner}</p>`;
};

// Strong / Emphasis
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.strong = function ({ tokens }: any) {
    return `<strong class="md-strong">${this.parser.parseInline(tokens)}</strong>`;
};
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.em = function ({ tokens }: any) {
    return `<em class="md-em">${this.parser.parseInline(tokens)}</em>`;
};

// Links
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.link = function ({ href, title, tokens }: any) {
    const t = title ? ` title="${escapeHtml(title)}"` : '';
    const h = href ?? '';
    const inner = this.parser.parseInline(tokens);
    return `<a class="md-a" href="${h}"${t}>${inner}</a>`;
};

// Images
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.image = function ({ href, title, text }: any) {
    const t = title ? ` title="${escapeHtml(title)}"` : '';
    const h = href ?? '';
    const alt = text ?? '';
    return `<img class="md-img" src="${h}" alt="${escapeHtml(alt)}"${t} />`;
};

// Lists
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.list = function (token: any) {
    const items = token.items || [];
    const body = items.map((item: any) => this.listitem(item)).join('');
    return token.ordered ? `<ol class="md-ol">${body}</ol>` : `<ul class="md-ul">${body}</ul>`;
};
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.listitem = function (item: any) {
    const inner = this.parser.parse(item.tokens);
    return `<li class="md-li">${inner}</li>`;
};

// Blockquote
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.blockquote = function ({ tokens }: any) {
    const inner = this.parser.parse(tokens);
    return `<blockquote class="md-blockquote">${inner}</blockquote>`;
};

// Horizontal rule
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.hr = () => '<hr class="md-hr" />';

// Inline code
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.codespan = function ({ text }: any) { return `<code class="md-code">${escapeHtml(text)}</code>`; };

// Code blocks
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.code = function ({ text, lang }: any) {
    const langClass = lang ? `language-${lang}` : '';
    const escaped = escapeHtml(text);
    return `<pre class="md-pre"><code class="${langClass} md-code">${escaped}</code></pre>`;
};

// Tables
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.table = function (token: any) {
    const theadCells = (token.header || []).map((cell: any) => this.tablecell(cell)).join('');
    const thead = `<thead class="md-thead">${this.tablerow({ text: theadCells })}</thead>`;
    const rows = token.rows || [];
    const tbodyRows = rows.map((row: any[]) => {
        const cells = row.map((cell: any) => this.tablecell(cell)).join('');
        return this.tablerow({ text: cells });
    }).join('');
    const tbody = `<tbody class="md-tbody">${tbodyRows}</tbody>`;
    return `<table class="md-table">${thead}${tbody}</table>`;
};
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.tablerow = function ({ text }: any) { return `<tr class="md-tr">${text}</tr>`; };
// @ts-expect-error - marked renderer signature differs across versions; runtime API is stable
renderer.tablecell = function (token: any) {
    const tag = token.header ? 'th' : 'td';
    const cls = token.header ? 'md-th' : 'md-td';
    const align = token.align ? ` style="text-align:${token.align}"` : '';
    const inner = this.parser.parseInline(token.tokens);
    return `<${tag} class="${cls}"${align}>${inner}</${tag}>`;
};

marked.use({
    renderer,
    gfm: true,
    breaks: true,
});

// @ts-expect-error - Custom options for marked
marked.setOptions({
    breaks: true,
    gfm: true
});

// Normalize ATX headings so that `#Title` (without a space) is treated as a heading
// This inserts a single space after 1–6 leading `#` when it's missing at the start of a line
function normalizeATXHeadings(input: string): string {
    // (^|\n) - start of string or newline
    // (\s{0,3}) - up to 3 optional leading spaces (per CommonMark)
    // (#{1,6}) - 1 to 6 hashes
    // ([^#\s]) - a character that is NOT a hash and NOT a space (ensures we don't split hashes or double-space)
    return input.replace(/(^|\n)(\s{0,3})(#{1,6})([^#\s])/g, '$1$2$3 $4');
}

// Normalize horizontal rules so they are always treated as HRs even without surrounding newlines
function normalizeHRs(input: string): string {
    // Matches ---, ***, or ___ on their own line (with optional trailing spaces)
    // and ensures they have at least one empty line before them to avoid being seen as Setext heading underlines
    // We use $2$2$2 to ensure we use the same character that was used in the match
    return input.replace(/([^\n])\n\s*([-*_])\2{2,}\s*(\n|$)/g, '$1\n\n$2$2$2\n');
}

export function useMarkdown() {
    const renderMarkdown = (source: string) => {
        // First, normalize headings such as `#Title` → `# Title` to support both forms
        const normalized = normalizeATXHeadings(source);

        // Normalize HRs to avoid Setext heading interpretation
        const withHRs = normalizeHRs(normalized);

        // We use a small hack to ensure spaces are preserved if they are followed by a newline
        // but only if there are at least two spaces (standard Markdown)
        const prepared = withHRs.replace(/ {2,}\n/g, '  <br>\n');

        // @ts-expect-error - marked.parse can return a promise if async, but we use it synchronously
        const rendered = marked.parse(prepared, { async: false });
        return DOMPurify.sanitize(rendered as string);
    };

    const highlightCode = () => {
        nextTick(() => {
            Prism.highlightAll();
        });
    };

    return {
        renderMarkdown,
        highlightCode,
    };
}
