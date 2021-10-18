<template>
    <div class="w-full h-full mb-4">
        <icon v-if="loading" name="radio-circle" animation="burst" size="32px" />
        <div v-show="!loading" id="map" class="w-full h-full rounded shadow border" />
    </div>
</template>

<script>
export default {
    name: 'LatlngInput',
    props: {
        value: [Object, String],
    },
    data () {
        return {
            map: null,
            marker: null,
            loading: false,
            geocoder: null,
        }
    },
    watch: {
        value (val) {
            this.searchLocation(val)
        },
    },
    mounted () {
        this.loading = true

        this.loadScript().then(() => {
            this.loadMap().then(() => this.loading = false)
        })
    },
    methods: {
        mark (pos) {
            if (!pos) return

            if (this.marker) this.marker.setPosition(pos)
            else {
                this.marker = new google.maps.Marker({
                    position: pos,
                    map: this.map,
                })
            }

            this.map.panTo(pos)
            this.$emit('input', pos)
        },
        getCurrentPosition () {
            return new Promise((resolve, reject) => {
                let pos = { lat: 3.1385036, lng: 101.6169487 }

                if (navigator.geolocation) {
                    navigator.geolocation.getCurrentPosition(
                        (position) => {
                            pos = {
                                lat: position.coords.latitude,
                                lng: position.coords.longitude,
                            }

                            resolve(pos)
                        }, 
                        (e) => {
                            console.error(e)
                            resolve(pos)
                        }
                    )
                }
                else {
                    console.error('Browser does not support geolocation')
                    resolve(pos)
                }
            })
        },
        loadScript () {
            return new Promise((resolve, reject) => {
                const script = document.createElement('script');
                script.src = 'https://maps.googleapis.com/maps/api/js?key=' + process.env.MIX_GOOGLE_CLOUD_API_KEY;
                script.async = true;
                script.onload = () => resolve()

                document.head.appendChild(script);
            })
        },
        loadMap () {
            return this.getCurrentPosition().then(center => {
                this.map = new google.maps.Map(this.$el.querySelector('#map'), { 
                    zoom: 17,
                    center,
                    mapTypeControl: false,
                    scaleControl: false,
                    streetViewControl: false,
                    rotateControl: false,
                    fullscreenControl: false,
                })

                google.maps.event.addListener(this.map, 'click', (event) => {
                    this.mark({
                        lat: event.latLng.lat(),
                        lng: event.latLng.lng(),
                    })
                })

                this.searchLocation(this.value)
            })
        },
        searchLocation (location) {
            if (!location) return
            if (!this.geocoder) this.geocoder = new google.maps.Geocoder()

            const config = {}

            if (typeof location === 'string') config.address = location
            else config.location = location

            this.geocoder.geocode(config, (res, status) => {
                if (status === 'OK' && res[0]) {
                    const pos = {
                        lat: res[0].geometry.location.lat(),
                        lng: res[0].geometry.location.lng(),
                    }

                    this.mark(pos)
                }
            })
        }
    }
}
</script>
