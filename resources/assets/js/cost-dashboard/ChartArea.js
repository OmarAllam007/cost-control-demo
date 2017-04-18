import Activity from './Activity';
import Boq from './Boq';
import Resource from './Resource';
import ResourceType from './ResourceType';
import c3 from 'c3';
import _ from 'lodash';

export default {
    data() {
        return {
            show_chart: false,
            type: '', filter: '', filter_items: [], loading: false, period,
            charts: {
                budget_vs_completion: {type: 'compare', name: 'Budget Cost VS at completion Cost'},
                todate_vs_allowable: {type: 'compare', name: 'Allowable Cost VS To date cost'},
                todate_var_trend: {type: 'trend', name: 'Cost Variance to date - Trend Analysis', default: 'To Date Cost Var'},
                completion_var_trend: {type: 'trend', name: 'Cost variance at completion - Trend Analysis', default: 'At Completion Cost Var'},
                completion_cost_trend: {type: 'trend', name: 'Cost at completion - Trend Analysis', default: 'Completion Cost'},
                todate_cost_trend: {type: 'trend', name: 'Cost to Date - Trend Analysis', default: 'To Date Cost'},
                cpi_trend: {type: 'trend', name: 'CPI Trend Analysis', default: 'CPI'},
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
                    this.show_chart = true;

                    if (this.chart.type === 'trend') {
                        this.renderTrend(response);
                    } else {
                        this.renderCompare(response);
                    }
                } else {
                    this.show_chart = false;
                }
                this.loading = false;
            }).error(() => {
                this.loading = false;
                this.show_chart = false;
            });
        },

        renderTrend(response) {
            let categories = [];
            let columns = [];
            for (let i in response.data) {
                categories.push(response.data[i].name);
            }
            categories = _.uniq(categories);
            let json = [], labels = [];
            for (let c in categories) {
                let category = categories[c];
                let column = {name: category};

                for (let i in response.data) {
                    let row = response.data[i];
                    if (row.name === category) {
                        let label = response.filter? row[response.filter] : this.chart.default;
                        label = label.replace(/^\d+\./g, '');
                        labels.push(label);
                        column[label] = row.value;
                    }
                }

                json.push(column);
            }

            c3.generate({
                bindto: this.$el.querySelector('.chart'),
                data: { json, keys: { x: 'name',  value: _.uniq(labels)}, type: 'line' },
                transition: { duration: 100 }, axis: { x: {type: 'category'} },
                grid: { x: { show: true }, y: {show: true}}
            });
        },

        renderCompare(response) {
            let categories = [];
            let columns = [];
            for (let i in response.data) {
                categories.push(response.data[i][response.filter]);
            }
            categories = _.uniq(categories);
            for (let c in response.columns) {
                let name = response.columns[c];
                let column = [name];
                for (let i in response.data) {
                    column.push(response.data[i][name]);
                }
                columns.push(column);
            }

            c3.generate({
                bindto: this.$el.querySelector('.chart'), data: { columns, type: 'bar' },
                bar: { width: {ratio: .5} }, transition: { duration: 100 },
                axis: { x: {type: 'category', categories} }, grid: { x: { show: true }, y: {show: true}}
            });
        },

        openFiltersModal() {
            $(this.$el).find('.modal').modal();
        }
    },

    computed: {
        can_run() {
            return this.type;
        },

        chart() {
            if (this.type && this.charts.hasOwnProperty(this.type)) {
                return this.charts[this.type];
            }

            return {};
        },

        show_period() {
            if (this.chart.type) {
                return this.chart.type == 'compare';
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