<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Response;

class DocumentationController extends Controller
{
    /**
     * Display documentation file
     *
     * @param string $filename
     * @return \Illuminate\Http\Response
     */
    public function show($filename)
    {
        $path = resource_path('docs/' . $filename);
        
        if (file_exists($path)) {
            $content = file_get_contents($path);
            
            // If it's a markdown file, convert to HTML
            if (pathinfo($path, PATHINFO_EXTENSION) === 'md') {
                $parser = new \cebe\markdown\GithubMarkdown();
                $content = '
                <!DOCTYPE html>
                <html>
                <head>
                    <title>Documentation</title>
                    <style>
                        body {
                            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif;
                            line-height: 1.6;
                            color: #333;
                            max-width: 800px;
                            margin: 0 auto;
                            padding: 20px;
                        }
                        h1, h2, h3, h4, h5, h6 {
                            margin-top: 1.5em;
                            margin-bottom: 0.5em;
                            color: #000;
                        }
                        h1 { font-size: 2em; border-bottom: 1px solid #eee; padding-bottom: 0.3em; }
                        h2 { font-size: 1.5em; border-bottom: 1px solid #eee; padding-bottom: 0.3em; }
                        code {
                            background-color: #f6f8fa;
                            padding: 0.2em 0.4em;
                            border-radius: 3px;
                            font-family: SFMono-Regular, Consolas, "Liberation Mono", Menlo, monospace;
                            font-size: 85%;
                        }
                        pre {
                            background-color: #f6f8fa;
                            border-radius: 3px;
                            padding: 16px;
                            overflow: auto;
                        }
                        pre code {
                            background-color: transparent;
                            padding: 0;
                        }
                        blockquote {
                            padding: 0 1em;
                            color: #6a737d;
                            border-left: 0.25em solid #dfe2e5;
                            margin: 0;
                        }
                        ul, ol {
                            padding-left: 2em;
                        }
                        hr {
                            height: 0.25em;
                            padding: 0;
                            margin: 24px 0;
                            background-color: #e1e4e8;
                            border: 0;
                        }
                    </style>
                </head>
                <body>
                    ' . $parser->parse($content) . '
                </body>
                </html>';
                
                return response($content)->header('Content-Type', 'text/html');
            }
            
            // Otherwise return the file as is
            return Response::make($content, 200, [
                'Content-Type' => mime_content_type($path),
                'Content-Disposition' => 'inline; filename="' . $filename . '"'
            ]);
        }
        
        abort(404);
    }
}
