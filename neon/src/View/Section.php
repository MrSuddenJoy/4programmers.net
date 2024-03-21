<?php
namespace Neon\View;

readonly class Section implements Item
{
    public function __construct(
        private string $rootBreadcrumb,
        private string $sectionTitle,
        private array  $children,
    )
    {
    }

    public function html(callable $h): array
    {
        return [
            $h('div', [
                $h('div', [
                    $h('nav', [
                        $h('ul', [
                            $h('li', [$this->rootBreadcrumb], 'inline'),
                            $h('span', ['/'], 'mx-1 text-[#00A538]'),
                            $h('li', ['Events'], 'inline'),
                        ], 'text-xs font-[Arial]'),
                    ]),
                    '<div style="width:580px; height:580px;  border-radius:580px; background:rgba(0, 165, 56, 0.60); filter:blur(50px); position:absolute; right:-290px; bottom:50%"></div>',
                    $h('h1', [$this->sectionTitle], 'font-semibold text-2xl'),
                ], 'bg-white rounded-lg py-5 p-4 mb-8 relative overflow-hidden'),
                $h('div',
                    ['Events with our patronage'],
                    'text-xs text-[#053B00] mb-4 tracking-tight'),
                ...\array_map(
                    fn(Event $event) => $event->html($h),
                    $this->children),
            ], 'container mx-auto my-4'),
        ];
    }
}