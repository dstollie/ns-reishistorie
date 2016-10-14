<template>
    <form>
        <fieldset>
            Van <input type="text" v-model="journeyInformation.fromStation">
            Naar <input type="text" v-model="journeyInformation.toStation">
            Datum <input type="text" v-model="journeyInformation.dateTime">
            <input @click="load" type="button" value="send">
        </fieldset>

        <pre><code class="language-json" v-html="apiOutput"></code></pre>

        <p v-if="loading">Data ophalen ...</p>

        <!--<pre><code class="language-php" v-html="test"></code></pre>-->

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
                apiOutput: "{ test: 'test'}",
                test: 'echo "test"',
                loading: false
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
                        this.apiOutput = content
                    })
                }, response => {
                    this.loading = false;
                })
            }
        }
    }
</script>