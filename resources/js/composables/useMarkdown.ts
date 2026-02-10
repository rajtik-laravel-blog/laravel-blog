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

export function useMarkdown() {
    const normalizeMarkdown = (source: string) => {
        if (!source) return '';

        // 1. Ensure space after # for headings at the start of a line
        // We use [^#\s] to ensure we only add a space if it's missing and not just more hashes
        let normalized = source.replace(/^(\s*#{1,6})([^#\s])/gm, '$1 $2');

        // 2. Handle the specific case where multiple headings are on the same line
        // Example: "#Title ##Subtitle" -> "# Title\n\n## Subtitle"
        normalized = normalized.split('\n').map(line => {
            if (/^\s*#{1,6}\s+/.test(line)) {
                // Find subsequent # markers that are preceded by space and NOT followed by space/#
                return line.replace(/(\s+)(#{1,6})([^#\s])/g, (match, p1, p2, p3) => {
                    return '\n\n' + p2 + ' ' + p3;
                });
            }
            return line;
        }).join('\n');

        return normalized;
    };

    const renderMarkdown = (source: string) => {
        const normalized = normalizeMarkdown(source);
        const rendered = marked.parse(normalized, {
            gfm: true,
            breaks: false,
        });
        return DOMPurify.sanitize(rendered as string);
    };

    const highlightCode = () => {
        nextTick(() => {
            Prism.highlightAll();
        });
    };

    return {
        normalizeMarkdown,
        renderMarkdown,
        highlightCode,
    };
}
