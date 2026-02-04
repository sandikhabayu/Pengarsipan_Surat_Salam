<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\SuratTemplate;
use HTMLPurifier;
use HTMLPurifier_Config;

class CleanSuratTemplateHTML extends Command
{
    protected $signature = 'surat:clean-html';
    protected $description = 'Clean HTML content in SuratTemplate';

    public function handle()
    {
        $this->info('Starting HTML cleanup...');
        
        $config = HTMLPurifier_Config::createDefault();
        $config->set('HTML.Allowed', 'p,br,b,strong,i,em,u,strike,sub,sup,div,span,ul,ol,li,table,thead,tbody,tr,th,td');
        $config->set('CSS.AllowedProperties', 'text-align,font-weight,font-style,text-decoration,border,border-collapse,width,padding,margin,vertical-align');
        $config->set('HTML.AllowedAttributes', 'p.align,div.align,div.style,td.align,td.rowspan,td.colspan,td.style,th.align,th.rowspan,th.colspan,th.style,table.border,table.cellpadding,table.cellspacing,table.width,table.align,table.style');
        $config->set('AutoFormat.AutoParagraph', false); // Ubah ke false
        $config->set('AutoFormat.RemoveEmpty', true);
        
        $purifier = new HTMLPurifier($config);
        
        $templates = SuratTemplate::all();
        $count = 0;
        
        foreach ($templates as $template) {
            if ($template->isi_surat) {
                $cleaned = $purifier->purify($template->isi_surat);
                
                // Hapus span dan atribut yang tidak perlu
                $cleaned = preg_replace('/<span[^>]*>/i', '', $cleaned);
                $cleaned = preg_replace('/<\/span>/i', '', $cleaned);
                $cleaned = preg_replace('/\s+lang="[^"]*"/i', '', $cleaned);
                $cleaned = preg_replace('/\s+dir="[^"]*"/i', '', $cleaned);
                
                // Preserve alignment by converting align to style
                $cleaned = preg_replace_callback('/<(p|div|td|th)\s+([^>]*\s)?align="([^"]+)"([^>]*)>/i', 
                    function($matches) {
                        return '<' . $matches[1] . ' ' . ($matches[2] ?? '') . 'style="text-align:' . $matches[3] . ';"' . $matches[4] . '>';
                    }, 
                    $cleaned
                );
                
                $template->isi_surat = $cleaned;
                $template->save();
                $count++;
            }
        }
        
        $this->info("Cleaned $count records.");
        return 0;
    }
}