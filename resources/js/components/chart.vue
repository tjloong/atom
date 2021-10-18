<template>
    <div class="chart" :class="type">
        <p class="has-text-centered has-text-grey" v-if="empty">
            <span class="has-text-weight-bold">No data to show</span>
            <small class="is-block">We do not have enough data to show the chart</small>
        </p>

        <canvas id="chart-canvas" v-else></canvas>
    </div><!-- / .chart -->
</template>

<script>
import Chart from 'chart.js'

export default {
    name: 'Chart',
    props: {
        data: Object,
        color: { default: 'primary' },
        type: { type: String, default: 'bar' },
        options: { type: Object, default: function() { return {} }},
    },
    data () {
        return {
            chart: null,
            colors: {
                primary: '#57B2D1',
                info: '#2D5F8E',
                warning: '#FFE66D',
                danger: '#FF6B6B',
                success: '#5EC198',
                dark: '#212A33',
                grey: '#97A9bC',
                light: '#CBD4DD',
                whiter: '#EAEDF1',
            },
        }
    },
    computed: {
        empty () {
            return _.isEmpty(this.data) || _.isEmpty(this.data.datasets)
        },
        chartOptions () {
            const options = {
                maintainAspectRatio: false,
                legend: { display: false },
            }

            if (['bar', 'line'].includes(this.type)) {
                options.scales = {
                    xAxes: [{
                        gridLines: { display: false },
                        ticks: { fontColor: '#ccc' },
                    }],
                    yAxes: [{
                        ticks: {
                            fontColor: '#ccc',
                            beginAtZero: true,
                            maxTicksLimit: 5,
                            callback: (label, index, labels) => {
                                return (_.isFinite(label) && label >= 1000) ? (label/1000) + 'K' : label
                            },
                        },

                    }],
                }
            }

            return options
        },
    },
    watch: {
        data (val) {
            this.draw()
        },
    },
    mounted () {
        this.draw()
    },
    methods: {
        draw () {
            if (this.empty) return

            this.data.datasets = this.data.datasets.map((d, i) => {
                if (['bar', 'line'].includes(this.type)) {
                    const color = Array.isArray(this.color) ? this.colors[this.color[i]] : this.colors[this.color]

                    if (!d.backgroundColor) d.backgroundColor = color
                    if (!d.borderColor) d.borderColor = color
                }
                else if (['pie', 'doughnut'].includes(this.type)) {
                    if (!d.backgroundColor) {
                        const colors = (Array.isArray(this.color) && this.color.length) ? _.pick(this.colors, this.color) : this.colors
                        d.backgroundColor = _.shuffle(Object.values(colors))
                    }
                }

                return d
            })

            this.chart = new Chart(this.$el.querySelector('#chart-canvas'), {
                type: this.type,
                data: this.data,
                options: Object.assign(this.chartOptions, this.options),
            })
        },
    },
}
</script>

<style lang="scss">
.chart {
    display: flex;
    flex-direction: column;
    justify-content: center;
    width: 100%;
    height: 18rem;

    &.doughnut {
        margin: 1rem auto 1rem;
    }
}
</style>
