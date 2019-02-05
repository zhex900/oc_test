<template>

    <v-card tile>
        <v-card-text>
            <v-btn fab dark small color="blue">
                <v-icon dark>home</v-icon>
            </v-btn>{{ membership.address }}</v-card-text>
        <v-card-text>
            <v-btn fab dark small color="red">
                <v-icon dark>local_post_office</v-icon>
            </v-btn> {{ membership.mailing_address }}</v-card-text>
    </v-card>
</template>

<script>

export default {
    name: "MembershipDetails",
    components: {},
    props: {
        membership: {
            type: Object,
            default: null
        }
    }
  }
</script>

<style lang="css" scoped>
</style>