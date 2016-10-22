<template>
    <form>
        <fieldset>
            Van <input type="text" v-model="journeyInformation.fromStation">
            Naar <input type="text" v-model="journeyInformation.toStation">
            Datum <input type="text" v-model="journeyInformation.dateTime">
            <input @click="load" type="button" value="send">
            <input @click="saveSession" type="button" value="save">
        </fieldset>


        <p v-if="loading">Data ophalen ...</p>

        <div class="row">
            <div class="col-md-6">
                <h2>Status: {{ status }}</h2>

                <pre><code class="language-json" v-html="apiOutput"></code></pre>
            </div>
            <div class="col-md-6">
                <stored-journeys v-on:restore="restoreSession" v-on:remove="removeSession"
                                 :sessions="sessions"></stored-journeys>
            </div>
        </div>

    </form>
</template>

<script>
    import Vue from "vue";
    import StoredJourneys from './StoredJourneys.vue'
    import _ from 'underscore';

    module.exports = {
        components: {
            StoredJourneys
        },
        mounted() {
            this.loadStoredJourneys();
        },
        watch: {
            'apiOutput': () => {
                Vue.nextTick(()=> {
                    window.Prism.highlightAll()
                })
            }
        },
        data() {
            return {
                journeyInformation: {
                    fromStation: 'Nijverdal',
                    toStation: 'Zwolle',
                    dateTime: '2016-10-11T20:00'
                },
                apiOutput: null,
                test: 'echo "test"',
                loading: false,
                sessions: []
            }

        },
        computed: {
            status() {

                if (!this.apiOutput) {
                    return null;
                }

                if (_.isObject(this.apiOutput)) {
                    return this.apiOutput.ReisMogelijkheid.Status;
                }

                if (_.isArray(this.apiOutput)) {
                    return this.apiOutput[0].ReisMogelijkheid.Status;
                }
            }
        },
        methods: {

            load() {
                this.loading = true;

                this.$http.get('/api/plan', {
                    params: this.journeyInformation
                }).then(response => {
                    this.loading = false;

                    response.json().then(content => {
                        this.apiOutput = content;
                    })
                }, response => {
                    this.loading = false;
                })
            },
            loadStoredJourneys() {
                this.loading = true;

                this.$http.get('/api/stored-journeys').then(response => {
                    response.json().then(content => {
                        this.loading = false;

                        this.sessions = content;
                    }, response => {
                        this.loading = false;
                    })
                })
            },
            restoreSession(session) {
                this.journeyInformation = session;
            },
            removeSession(sessionIndex) {
                this.sessions = _.reject(this.sessions, (session, index) => {
                    return sessionIndex == index;
                });

                this.loading = true;

                this.$http.post('/api/stored-journeys/remove', JSON.parse(JSON.stringify(this.sessions[sessionIndex]))).then(response => {
                    response.json().then(() => {
                        this.loadStoredJourneys();
                    }, () => {
                        this.loading = false;
                    }).bind(this)
                }).bind(this);
            },
            saveSession() {
                this.sessions.push(JSON.parse(JSON.stringify(this.journeyInformation)));

                this.loading = true;

                this.$http.post('/api/stored-journeys', JSON.parse(JSON.stringify(this.journeyInformation))).then(response => {
                    response.json().then(() => {
                        this.loadStoredJourneys();
                    }, () => {
                        this.loading = false;
                    }).bind(this)
                }).bind(this);
            }
        }
    }
</script>