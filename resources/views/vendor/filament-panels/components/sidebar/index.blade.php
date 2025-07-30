@props([
    'navigation',
])

@php
    $openSidebarClasses = 'fi-sidebar-open w-[--sidebar-width] translate-x-0 shadow-xl ring-1 ring-gray-950/5 dark:ring-white/10 rtl:-translate-x-0';
    $isRtl = __('filament-panels::layout.direction') === 'rtl';
@endphp

<aside
    x-data="{}"
    @if (filament()->isSidebarCollapsibleOnDesktop() && (! filament()->hasTopNavigation()))
        x-cloak
        x-bind:class="
            $store.sidebar.isOpen
                ? @js($openSidebarClasses . ' ' . 'lg:sticky')
                : '-translate-x-full rtl:translate-x-full lg:sticky lg:translate-x-0 rtl:lg:-translate-x-0'
        "
    @else
        @if (filament()->hasTopNavigation())
            x-cloak
            x-bind:class="$store.sidebar.isOpen ? @js($openSidebarClasses) : '-translate-x-full rtl:translate-x-full'"
        @elseif (filament()->isSidebarFullyCollapsibleOnDesktop())
            x-cloak
            x-bind:class="$store.sidebar.isOpen ? @js($openSidebarClasses . ' ' . 'lg:sticky') : '-translate-x-full rtl:translate-x-full'"
        @else
            x-cloak="-lg"
            x-bind:class="
                $store.sidebar.isOpen
                    ? @js($openSidebarClasses . ' ' . 'lg:sticky')
                    : 'w-[--sidebar-width] -translate-x-full rtl:translate-x-full lg:sticky'
            "
        @endif
    @endif
    {{
        $attributes->class([
            'fi-sidebar fixed inset-y-0 start-0 z-30 flex flex-col h-screen content-start bg-white transition-all duration-300 ease-in-out dark:bg-gray-900 lg:z-0 lg:bg-transparent lg:shadow-none lg:ring-0 lg:transition-none dark:lg:bg-transparent',
            'lg:translate-x-0 rtl:lg:-translate-x-0' => ! (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop() || filament()->hasTopNavigation()),
            'lg:-translate-x-full rtl:lg:translate-x-full' => filament()->hasTopNavigation(),
        ])
    }}
>
    <!-- Sidebar Header with Enhanced Styling -->
    <div class="overflow-x-clip border-b border-gray-100 dark:border-gray-800">
        <header class="fi-sidebar-header flex h-16 items-center bg-white px-6 ring-1 ring-gray-950/5 dark:bg-gray-900 dark:ring-white/10 lg:shadow-sm">
            <!-- Logo Section with Smooth Transition -->
            <div
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-show="$store.sidebar.isOpen"
                    x-transition:enter="lg:transition lg:delay-100"
                    x-transition:enter-start="opacity-0"
                    x-transition:enter-end="opacity-100"
                @endif
                class="transition-opacity duration-200"
            >
                @if ($homeUrl = filament()->getHomeUrl())
                    <a {{ \Filament\Support\generate_href_html($homeUrl) }} class="block hover:opacity-80 transition-opacity">
                        <x-filament-panels::logo class="h-8 w-auto" />
                    </a>
                @else
                    <x-filament-panels::logo class="h-8 w-auto" />
                @endif
            </div>

            <!-- Expand/Collapse Buttons with Better Visual Feedback -->
            @if (filament()->isSidebarCollapsibleOnDesktop())
                <x-filament::icon-button
                    color="gray"
                    :icon="$isRtl ? 'heroicon-o-chevron-left' : 'heroicon-o-chevron-right'"
                    :icon-alias="$isRtl ? ['panels::sidebar.expand-button.rtl', 'panels::sidebar.expand-button'] : 'panels::sidebar.expand-button'"
                    icon-size="lg"
                    :label="__('filament-panels::layout.actions.sidebar.expand.label')"
                    x-cloak
                    x-data="{}"
                    x-on:click="$store.sidebar.open()"
                    x-show="! $store.sidebar.isOpen"
                    class="mx-auto hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200"
                />
            @endif

            @if (filament()->isSidebarCollapsibleOnDesktop() || filament()->isSidebarFullyCollapsibleOnDesktop())
                <x-filament::icon-button
                    color="gray"
                    :icon="$isRtl ? 'heroicon-o-chevron-right' : 'heroicon-o-chevron-left'"
                    :icon-alias="$isRtl ? ['panels::sidebar.collapse-button.rtl', 'panels::sidebar.collapse-button'] : 'panels::sidebar.collapse-button'"
                    icon-size="lg"
                    :label="__('filament-panels::layout.actions.sidebar.collapse.label')"
                    x-cloak
                    x-data="{}"
                    x-on:click="$store.sidebar.close()"
                    x-show="$store.sidebar.isOpen"
                    class="ms-auto hidden lg:flex hover:bg-gray-50 dark:hover:bg-gray-800 transition-colors duration-200"
                />
            @endif
        </header>
    </div>

    <!-- Navigation Content with Improved Spacing and Scroll Behavior -->
    <nav class="fi-sidebar-nav flex-grow flex flex-col gap-y-6 overflow-y-auto overflow-x-hidden px-4 py-6" style="scrollbar-gutter: stable">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_START) }}

        <!-- Tenant Menu with Card-like Appearance -->
        @if (filament()->hasTenancy() && filament()->hasTenantMenu())
            <div
                @class([
                    'fi-sidebar-nav-tenant-menu-ctn transition-all duration-200 mb-4',
                    '-mx-2' => ! filament()->isSidebarCollapsibleOnDesktop(),
                ])
                @if (filament()->isSidebarCollapsibleOnDesktop())
                    x-bind:class="$store.sidebar.isOpen ? '-mx-2' : '-mx-4'"
                @endif
            >
                <div class="bg-gray-50 dark:bg-gray-800 rounded-lg p-3 shadow-sm">
                    <x-filament-panels::tenant-menu />
                </div>
            </div>
        @endif

        <!-- Navigation Groups with Better Visual Hierarchy -->
        <ul class="fi-sidebar-nav-groups -mx-2 flex flex-col gap-y-4">
            @foreach ($navigation as $group)
                <x-filament-panels::sidebar.group
                    :active="$group->isActive()"
                    :collapsible="$group->isCollapsible()"
                    :icon="$group->getIcon()"
                    :items="$group->getItems()"
                    :label="$group->getLabel()"
                    :attributes="\Filament\Support\prepare_inherited_attributes($group->getExtraSidebarAttributeBag())"
                    class="bg-gray-50/50 dark:bg-gray-800/50 rounded-lg p-2 hover:bg-gray-100 dark:hover:bg-gray-700/50 transition-colors duration-200"
                />
            @endforeach
        </ul>

        <!-- Enhanced Collapsed Groups Persistence Script -->
        <script>
            document.addEventListener('DOMContentLoaded', function() {
                try {
                    var collapsedGroups = JSON.parse(
                        localStorage.getItem('collapsedGroups') || 'null'
                    );

                    if (collapsedGroups === null) {
                        localStorage.setItem(
                            'collapsedGroups',
                            JSON.stringify(@js(
                                collect($navigation)
                                    ->filter(fn (\Filament\Navigation\NavigationGroup $group): bool => $group->isCollapsed())
                                    ->map(fn (\Filament\Navigation\NavigationGroup $group): string => $group->getLabel())
                                    ->values()
                                    ->all()
                            ))
                        );
                    }

                    collapsedGroups = JSON.parse(
                        localStorage.getItem('collapsedGroups') || '[]'
                    );

                    document.querySelectorAll('.fi-sidebar-group').forEach((group) => {
                        if (!group.dataset.groupLabel || !collapsedGroups.includes(group.dataset.groupLabel)) {
                            return;
                        }

                        const items = group.querySelector('.fi-sidebar-group-items');
                        if (items) {
                            items.style.display = 'none';
                            items.style.opacity = '0';
                        }

                        const collapseBtn = group.querySelector('.fi-sidebar-group-collapse-button');
                        if (collapseBtn) {
                            collapseBtn.classList.add('rotate-180');
                            collapseBtn.classList.add('text-primary-500');
                        }
                    });
                } catch (e) {
                    console.error('Error handling sidebar collapsed state:', e);
                }
            });
        </script>

        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_NAV_END) }}
    </nav>

    <!-- Sidebar Footer with Subtle Styling -->
    <div class="border-t border-gray-100 dark:border-gray-800 py-4 px-6">
        {{ \Filament\Support\Facades\FilamentView::renderHook(\Filament\View\PanelsRenderHook::SIDEBAR_FOOTER) }}
    </div>
</aside>
