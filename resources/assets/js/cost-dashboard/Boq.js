export default {
    template: document.getElementById('boq-template').innerHTML,

    data() {
        return {
            items: boqs, selected: [], term: ''
        };
    },
    watch: {
        selected() {
            this.$dispatch('update_items', this.selected);
        }
    },

    computed: {
        filtered() {
            if (!this.term) {
                return this.items;
            }

            const code = this.term.toLowerCase();
            return this.items.filter(item => {
                return item.cost_account.toLowerCase().includes(code) || item.description.toLowerCase().includes(code);
            });
        }
    },

    methods: {
        apply() {
            this.$dispatch('update_filters', this.selected)
        },

        toggleItem(id, e) {
            if (this.selected.length >= 10 && event.target.checked) {
                alert('Only 10 items allowed');
                e.target.checked = false;
                e.preventDefault();
                return false;
            }

            let newSelected = [];
            let found = false;

            for (let i = 0; i < this.selected.length; ++i) {
                if (id != this.selected[i]) {
                    newSelected.push(this.selected[i]);
                } else {
                    found = true;
                }
            }

            if (found) {
                this.selected = newSelected;
            } else {
                this.selected.push(id);
            }
        }
    }
}