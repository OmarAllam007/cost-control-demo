import Activity from './Activity';
import Boq from './Boq';
import Resource from './Resource';
import ResourceType from './ResourceType';
import c3 from 'c3';

export default {
    data() {
        return {
            show_chart: false,
            type: '', filter: '', filter_items: [], loading: false, period,
            charts: {
                budget_vs_completion: 'compare', todate_vs_allowable: 'compare',
                todate_var_trend: 'trend', completion_var_trend: 'trend', completion_cost_trend: 'trend',
                todate_cost_trend: 'trend', cpi_trend: 'trend',
            }
        };
    },

    methods: {
        run () {
            this.show_chart = true;
            this.loading = true;
            $.ajax({
                url: '/project/' + project_id + '/charts', type: 'post', dataType: 'json',
                data: {
                    _token: $('[name=csrf-token]').attr('content'), type: this.type,  filter: this.filter,
                    period_id: this.period,  filter_items: this.filter_items
                }
            }).success(response => {
                if (response.ok) {
                    let categories = [];
                    let columns = [];

                    for (let i in response.data) {
                        categories.push(response.data[i][response.filter]);
                    }

                    for (let c in response.columns) {
                        let name = response.columns[c];
                        let column = [name];
                        for (let i in response.data) {
                            column.push(response.data[i][name]);
                        }
                        columns.push(column);
                    }

                    let type = 'bar';

                    this.show_chart = true;
                    c3.generate({
                        bindto: this.$el.querySelector('.chart'),
                        data: {columns, type },
                        bar: { width: {ratio: .5} },
                        transition: { duration: 100 },
                        axis: { x: {type: 'category', categories} },
                        grid: { x: { show: true }, y: {show: true}}
                    });
                } else {
                    this.show_chart = false;
                }
                this.loading = false;
            }).error(() => {
                this.loading = false;
                this.show_chart = false;
            });
        },

        openFiltersModal() {
            $(this.$el).find('.modal').modal();
        }
    },

    computed: {
        can_run() {
            return this.type && this.filter && this.filter_items.length;
        },

        show_period() {
            if (this.charts.hasOwnProperty(this.type)) {
                return this.charts[this.type] == 'compare';
            }

            return true;
        }
    },

    events: {
        update_items(items) {
            this.filter_items = items;
        }
    },

    components: {
        Activity, Boq, Resource, ResourceType
    }
}