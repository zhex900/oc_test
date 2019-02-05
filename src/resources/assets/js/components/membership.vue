<template>

    <td class="text-xs-left">
        <div v-if="membership==null">
            <v-btn color="grey" class="white--text" small dark>
                Guest
                <v-icon>face</v-icon>
            </v-btn>
        </div>
        <div v-else>
            <v-btn :color="membershipColors[membership.name.toLowerCase()]" class="white--text" small dark>
                {{membership.name}}
                <v-icon>how_to_reg</v-icon>
            </v-btn>
        </div>
    </td>
</template>

<script>

import { format, parse, distanceInWordsToNow, differenceInCalendarDays } from "date-fns";

export default {
    name: "Membership",
    components: {},
    props: {
        membership: {
        type: Object,
        default: null
        }
    },
    data: () => ({
      membershipColors:{
          'silver':'blue lighten-3',
          'gold':'yellow darken-2',
          'platinum': 'indigo accent-4'
      }
    })
  }
</script>

<style lang="css" scoped>
</style>