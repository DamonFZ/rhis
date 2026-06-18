<x-filament-panels::page>
    <link href="https://cdn.mobiscroll.com/5.32.1/css/mobiscroll.javascript.min.css" rel="stylesheet" />

    <div id="mobiscroll-calendar" class="w-full h-[calc(100vh-12rem)]"></div>

    <script src="https://cdn.mobiscroll.com/5.32.1/js/mobiscroll.javascript.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function () {
            mobiscroll.setOptions({
                theme: 'ios',
                themeVariant: 'light',
                locale: mobiscroll.localeZh,
            });

            mobiscroll.eventcalendar('#mobiscroll-calendar', {
                view: {
                    calendar: { type: 'month', labels: true },
                    agenda: { type: 'month' },
                },
                data: @js($this->appointments),
                onEventClick: function (args) {
                    console.log(args.event);
                }
            });
        });
    </script>
</x-filament-panels::page>
