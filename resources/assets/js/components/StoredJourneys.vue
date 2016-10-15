<template>
    Existing sessions

    <ul>
        <li v-for="(session, index) in sessions">
            {{ session.fromStation }} {{ session.toStation }} {{ session.dateTime }} <input type="button" value="restore" @click="restore(session)" />  <input type="button" value="remove" @click="remove(index)" />
        </li>
    </ul>
</template>

<script>
    module.exports = {
        props: ['sessions'],

        watch: {
            'sessions': (sessions) => {
                console.log('change', sessions);
                window.localStorage.setItem('sessions', JSON.stringify(sessions));
            }
        },

        methods: {
            restore(session) {
                this.$emit('restore', session);
            },
            remove(index) {
                this.$emit('remove', index);
            }
        }
    }
</script>