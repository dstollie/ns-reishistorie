<template>
    <form>
        <fieldset>
            Van <input type="text" v-model="journeyInformation.fromStation">
            Naar <input type="text" v-model="journeyInformation.toStation">
            Datum <input type="text" v-model="journeyInformation.dateTime">
            <input @click="load" type="button" value="send">
        </fieldset>

        <p v-if="loading">Data ophalen ...</p>

        <h2>Status: {{ status }}</h2>

        <pre><code class="language-json" v-html="apiOutput"></code></pre>

    </form>
</template>

<script>
    import Vue from "vue";

    module.exports = {
        data() {
            return {
                journeyInformation: {
                    fromStation: 'Nijverdal',
                    toStation: 'Zwolle',
                    dateTime: '2016-10-11T20:00'
                },
                apiOutput: null,
                test: 'echo "test"',
                loading: false
            }

        },
        computed: {
            status() {
                return this.apiOutput ? this.apiOutput.ReisMogelijkheid.Status : null
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

                        Vue.nextTick(()=> {
                            window.Prism.highlightAll()
                        })
                    })
                }, response => {
                    this.loading = false;
                })
            }
        }
    }
</script>