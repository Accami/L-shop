<?php
declare(strict_types = 1);

namespace App\Composers;

use App\Contracts\ComposerContract;
use Illuminate\View\View;

/**
 * Class GlobalLayoutComposer
 *
 * @author  D3lph1 <d3lph1.contact@gmail.com>
 * @package App\Composers
 */
class GlobalLayoutComposer implements ComposerContract
{
    /**
     * {@inheritdoc}
     */
    public function compose(View $view): void
    {
        $view->with($this->getData());
    }

    /**
     * @return array
     */
    private function getData(): array
    {
        return [
            'shopDescription' => s_get('shop.description'),
            'shopKeywords' => s_get('shop.keywords')
        ];
    }
}