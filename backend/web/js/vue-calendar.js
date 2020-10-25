+function () {
    function removeFromArr(arr1, arr2) {
        const res = [];
        for (let i = 0; i < arr1.length; i++) {
            if (!arr2.some(o => o === arr1[i])) {
                res.push(arr1[i]);
            }
        }
        return res;
    }

    Vue.component('p-calendar', {
        template: '#vue-calendar-template',
        props: ['value'],
        data() {
            const now = dayjs()
            return {
                days: '日一二三四五六'.split(''),
                numCells: 6 * 7,
                current: '',
                selectedDays: [],
                selected: {
                    year: now.year(),
                    month: now.month(),
                    date: now.date()
                }
            }
        },
        mounted() {
            this.fromValue();
        },
        watch: {
            value() {
                this.fromValue();
            },
            'selected.year'() {
                this.selectedDays = [];
                this.$emit('update:dates', this.selectedDays);
            },
            'selected.month'() {
                this.selectedDays = [];
                this.$emit('update:dates', this.selectedDays);
            }

        },
        methods: {
            setMonth(dir) {
                const d = this.rawDate.add(+dir, 'month');
                this.selected.year = d.year();
                this.selected.month = d.month();
                this.selectedDays = [];
                this.updateDates();
            },
            fromValue() {

                if (this.value) {
                    const date = dayjs(this.value);
                    this.selected.year = date.year();
                    this.selected.month = date.month();
                    this.selected.date = date.date();
                }
                this.current = this.value;
            },
            getSlotName(date) {
                const {year, month} = this.selected;
                const d = dayjs().set('year', year).set('month', month).set('date', date);
                if (d.isValid()) {
                    return d.format('YYYY-MM-DD');
                }
                return false;
            },

            handleAll(evt) {
                this.selectedDays = evt.currentTarget.checked ? this.allDate.map(o => o.format('YYYY-MM-DD')) : [];
                this.updateDates();
            },
            updateDates() {
                this.$emit('update:dates', Array.from(new Set(this.selectedDays)));

            },

            handleDay(evt, day) {
                const arr = this.allDate.filter(o => o.day() === day).map(o => o.format('YYYY-MM-DD'));
                if (evt.target.checked) {
                    this.selectedDays.push(...arr);
                } else {
                    this.selectedDays = removeFromArr(this.selectedDays, arr);
                }
                this.updateDates();
            },
            handleRow(evt, data) {
                const arr = data.filter(o => o.val).map(o => {
                    return this.rawDate.set('date', o.val).format('YYYY-MM-DD');
                });
                if (evt.target.checked) {
                    this.selectedDays.push(...arr);
                } else {
                    this.selectedDays = removeFromArr(this.selectedDays, arr);
                }
                this.updateDates();

            },

            getTdClass(date) {
                const {year, month} = this.selected;
                const d = dayjs().set('year', year).set('month', month).set('date', date).format('YYYY-MM-DD');
                const obj = {};
                if (this.selectedDays.indexOf(d) >= 0) {
                    obj.selected = true;
                }
                if (this.current) {
                    if (d === this.current) {
                        obj.current = true;
                    }
                }

                return obj;
            },
            handleCell(val) {
                if (!val) return;
                this.current = this.rawDate.set('date', val).format('YYYY-MM-DD');
                this.$emit('input', this.current);

            },
            getCellValue(val) {
                if (!val) return '';
                return this.rawDate.set('date', val).format('YYYY-MM-DD');
            }
        },
        computed: {
            rawDate() {
                const {year, month, date} = this.selected;
                return dayjs().set('year', year).set('month', month).set('date', date);
            },
            allDate() {
                const date = this.rawDate.set('date', 1);
                return Array.from({length: date.daysInMonth()}, (k, i) => {
                    return date.set('date', i + 1);
                });
            },
            cells() {
                const days = this.rawDate.daysInMonth()
                const firstDay = this.rawDate.set('date', 1).day()
                const arr1 = Array.from({length: firstDay}).map(() => ({}))
                const arr2 = Array.from({length: days}).map((o, index) => {
                    return {
                        val: index + 1
                    }
                })
                const left = 7 - (arr1.length + arr2.length) % 7
                const arr3 = left !== 7 ? Array.from({length: left}, () => ({})) : [];

                return [...arr1, ...arr2, ...arr3]
            },
            formatCells() {
                const arr = []
                for (let i = 0; i <= this.cells.length; i += 7) {
                    arr.push(this.cells.slice(i, i + 7))
                }
                return arr
            }
        }
    });
}()
