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

                    const report_type = this.charts[this.type];

                    for (let i in response.data) {
                        if (report_type == 'trend') {
                            categories.push(response.data[i].name);
                        } else {
                            categories.push(response.data[i][response.filter]);
                        }
                    }

                    categories = _.uniq(categories);
                    const type = report_type === 'trend' ? 'line' : 'bar';
                    if (report_type == 'trend') {
                        this.show_chart = true;
                        let json = [], labels = [];
                        for (let c in categories) {
                            let category = categories[c];
                            let column = {name: category};

                            for (let i in response.data) {
                                let row = response.data[i];
                                if (row.name == category) {
                                    let label = row[response.filter];
                                    labels.push(label);
                                    column[label] = row.value;
                                }
                            }

                            json.push(column);
                        }

                        c3.generate({
                            bindto: this.$el.querySelector('.chart'),
                            data: { json, keys: { x: 'name',  value: _.uniq(labels)}, type },
                            transition: { duration: 100 },
                            axis: { x: {type: 'category'} },
                            grid: { x: { show: true }, y: {show: true}}
                        });
                    } else {
                        this.show_chart = true;
                        for (let c in response.columns) {
                            let name = response.columns[c];
                            let column = [name];
                            for (let i in response.data) {
                                column.push(response.data[i][name]);
                            }
                            columns.push(column);
                        }

                        c3.generate({
                            bindto: this.$el.querySelector('.chart'),
                            data: { columns, type },
                            bar: { width: {ratio: .5} },
                            transition: { duration: 100 },
                            axis: { x: {type: 'category', categories} },
                            grid: { x: { show: true }, y: {show: true}}
                        });
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

        openFiltersModal() {
            $(this.$el).find('.modal').modal();
        }
    },

    computed: {
        can_run() {
            return this.type;
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