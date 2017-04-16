export default {
    template: document.getElementById('resource-type-template').innerHTML,

    data() {
        return {
            items: resourceTypes, selected: []
        };
    },

    watch: {
        selected() {
            this.$dispatch('update_items', this.selected);
        }
    },

    methods: {
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