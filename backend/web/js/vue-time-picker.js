+function () {
    function fill(val) {
        return val < 10 ? ('0' + val) : val;

    }

    Vue.component('vue-time-picker', {
        template: '#vue-time-picker',
        props: ['value'],
        filters: {
            fill,
        },
        data() {
            return {
                isShow: false,
                selected: {
                    hour: '',
                    minute: '',
                    second: '',
                }
            }
        },
        computed: {
            isAll() {
                return Object.keys(this.selected).every(k => !!(this.selected[k].toString().length));
            },
            display() {
                if (!this.isAll) {
                    return '';
                }
                const {hour, minute, second} = this.selected;
                return [hour, minute, second].filter(o => !!o.toString().length).map(fill).join(":")
            }
        },
        mounted() {
            document.addEventListener('click', evt => {
                if (this.$el.contains(evt.target)) {
                    return;
                }
                this.close();
            });
            if (this.value) {
                const arr = this.value.split(':');
                this.selected.hour = arr[0] || 0;
                this.selected.minute = arr[1] || 0;
                this.selected.second = arr[2] || 0;
            }
        },
        methods: {
            show() {
                this.isShow = true;
            },
            close() {
                this.isShow = false;
            },
            setTime(key, val) {
                this.selected[key] = val;
                this.$emit('input', this.display);
            }
        }

    });
}()
