<template>
    <td v-if="membership!=null" class="text-xs-left">{{ `${parseDate(membership.expiry)}, `}}
        <span :class="closeToExpire(membership.expiry)"> {{`${until(membership.expiry)} left`}}</span>
    </td>
</template>

<script>

import { format, parse, distanceInWordsToNow, differenceInCalendarDays } from "date-fns";

export default {
    name: "Expiry",
    components: {},
    props: {
        membership: {
        type: Object,
        default: null
        }
    },
    methods: {
        closeToExpire(date){
            let diff = differenceInCalendarDays(parse(date), new Date() );
            if(diff<=30){
                return 'blinking';
            }else{
                return'';
            }
        },
        parseDate(date){
            return format(parse(date),'ddd DD/MM/YYYY');
        },
        until(date){
            return distanceInWordsToNow(parse(date))
        }
    }
  }
</script>

<style lang="css" scoped>
.blinking {
  animation: blinkingText 3s infinite;
  font-weight: bold;
}
@keyframes blinkingText {
  0% {
    color: red;
  }
  49% {
    color: brown;
  }
  50% {
    color: black;
  }
  99% {
    color: gray;
  }
  100% {
    color: red;
  }
}
</style>