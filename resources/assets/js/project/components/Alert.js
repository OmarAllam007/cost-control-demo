export default {
    template: `
<div v-show="show" class="alert alert-{{type}}">
    <button type="button" class="close" @click="show = false">
      <span aria-hidden="true">&times;</span>
    </button>
    
    <i class="fa fa-{{icon}}"></i> {{message}}
</div>
`,

    data() {
        return {
            icons: {
                info: 'info-circle',
                success: 'check-circle',
                warning: 'exclamation-triangle',
                danger: 'exclamation-circle',
                error: 'exclamation-circle',
            },
            show: false,
            message: '',
            type: ''
        }
    },

    methods: {
        showAlert() {
            this.show = true;
            setTimeout(() => {
                this.show = false
            }, 7000);
        }
    },

    events: {
        show_alert(alert) {
            this.message = alert.message;
            this.type = alert.type;
            this.showAlert();
        }
    },

    computed: {
        icon() {
            return this.icons.hasOwnProperty(this.type) ? this.icons[this.type] : this.icons['info'];
        }
    }
};