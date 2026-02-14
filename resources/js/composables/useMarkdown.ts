import rehypeShiki from '@shikijs/rehype';
import DOMPurify from 'dompurify';
import rehypeClassNames from 'rehype-class-names';
import rehypeStringify from 'rehype-stringify';
import remarkBreaks from 'remark-breaks';
import remarkGfm from 'remark-gfm';
import remarkParse from 'remark-parse';
import remarkRehype from 'remark-rehype';
import { unified } from 'unified';
import { ref } from 'vue';

export function useMarkdown() {
    const isReady = ref(false);
    let processor: any = null;

    const initProcessor = async () => {
        if (processor) {
            return;
        }

        processor = unified()
            .use(remarkParse)
            .use(remarkGfm)
            .use(remarkBreaks)
            .use(remarkRehype)
            .use(rehypeShiki, {
                theme: 'github-dark',
            })
            .use(rehypeClassNames, {
                h1: 'md-h1',
                h2: 'md-h2',
                h3: 'md-h3',
                h4: 'md-h4',
                h5: 'md-h5',
                h6: 'md-h6',
                p: 'md-p',
                a: 'md-a',
                ul: 'md-ul',
                ol: 'md-ol',
                li: 'md-li',
                blockquote: 'md-blockquote',
                hr: 'md-hr',
                code: 'md-code',
                pre: 'md-pre',
                strong: 'md-strong',
                em: 'md-em',
                img: 'md-img',
                table: 'md-table',
                thead: 'md-thead',
                tbody: 'md-tbody',
                tr: 'md-tr',
                th: 'md-th',
                td: 'md-td',
            })
            .use(rehypeStringify);

        isReady.value = true;
    };

    // Initialize immediately
    initProcessor();

    const renderMarkdown = async (source: string) => {
        if (!processor) {
            await initProcessor();
        }
        const prepared = source.replace(/ {2,}\n/g, '  <br>\n');

        const result = await processor.process(prepared);
        return DOMPurify.sanitize(result.toString());
    };

    const highlightCode = () => {
        // Shiki highlights during processing, so this is now a no-op
        // Keeping it for backward compatibility with components that call it
    };

    return {
        renderMarkdown,
        highlightCode,
        isReady,
    };
}
