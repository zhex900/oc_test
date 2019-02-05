<template>
    <v-app>
        <v-toolbar :color="filterIndicator[filterOn]" dark fixed>
            <v-toolbar-side-icon @click="showFilters = !showFilters">
                <div class="elevation-1">
                    <v-icon>filter_list</v-icon>
                    {{isFilterOn}}
                </div>
            </v-toolbar-side-icon>

            <v-autocomplete v-model="selectedPerson" :items="items" :loading="isLoading" :search-input.sync="search" color="white" cache-items flat hide-no-data hide-details item-text="searchable" item-value="first_name" label="Client Lookup" placeholder="Search by first name, last name or phone number" prepend-icon="mdi-database-search" return-object>
                <template slot="selection" slot-scope="data">
                    <v-chip color="rgb(74, 164, 114)" :selected="data.selected" close @input="clear">
                        <v-avatar>
                            <img :src="data.item.image">
                        </v-avatar>
                        {{ data.item.searchable }}
                    </v-chip>
                </template>
                <template slot="item" slot-scope="data">
                    <template>
                        <v-list-tile-avatar>
                            <img :src="data.item.image">
                        </v-list-tile-avatar>
                        <v-list-tile-content>
                            <v-list-tile-title v-html="`${data.item.first_name} ${data.item.last_name}`"></v-list-tile-title>
                            <v-list-tile-sub-title v-html="data.item.phone"></v-list-tile-sub-title>
                        </v-list-tile-content>
                    </template>
                </template>
            </v-autocomplete>

            <v-btn icon @click="query(selectedPerson.first_name)">
                <v-icon>search</v-icon>
            </v-btn>
            <v-btn icon @click="query('')">
                <v-icon>refresh</v-icon>
            </v-btn>
        </v-toolbar>

        <v-content style="padding-top: 60px">
            <v-card>
                <v-card-title>
                    <v-img :src="logo"></v-img>
                    <span class="logo">Client Lookup</span>
                    <v-spacer></v-spacer>
                    <v-text-field v-model="tableSearch" append-icon="search" label="Refine search" single-line hide-details></v-text-field>
                </v-card-title>
                <v-data-table :headers="headers" :items="items" :search="tableSearch" :pagination.sync="pagination">
                    <template slot="items" slot-scope="props">
                        <tr @click="props.expanded = !props.expanded">
                            <td class="text-xs-left">{{ props.item.id }}</td>
                            <td>
                                <v-avatar size="40" color="grey lighten-4">
                                    <img :src="props.item.image" alt="avatar">
                                </v-avatar>

                                <span style="padding-left:20px">{{ props.item.first_name }}</span>
                            </td>
                            <td class="text-xs-left">{{ props.item.last_name }}</td>
                            <td class="text-xs-left">{{ props.item.phone }}</td>

                            <Membership :membership="props.item.membership"></Membership>
                            <Expiry :membership="props.item.membership"></Expiry>
                        </tr>
                    </template>
                    <v-alert slot="no-results" :value="true" color="error" icon="warning">
                        Your search for "{{ tableSearch }}" found no results.
                    </v-alert>

                    <template slot="expand" slot-scope="props">
                        <v-card tile>
                            <v-card-text>
                                <v-btn fab dark small color="blue">
                                    <v-icon dark>home</v-icon>
                                </v-btn>{{ props.item.address }}</v-card-text>
                            <v-card-text>
                                <v-btn fab dark small color="red">
                                    <v-icon dark>local_post_office</v-icon>
                                </v-btn> {{ props.item.mailing_address }}</v-card-text>
                        </v-card>
                    </template>
                </v-data-table>
            </v-card>
        </v-content>
        <v-bottom-sheet inset v-model="showFilters">
            <v-card flat class="white--text" :color="filterIndicator[filterOn]">
                <v-card-title>
                    <v-icon>filter_list</v-icon>
                    <h4>Filters {{isFilterOn}}</h4>
                </v-card-title>
                <v-container fluid grid-list-lg>
                    <v-layout align-center justify-space-around row wrap>
                        <v-flex xs12 sm6>
                            <v-text-field box v-model="filterFirstName" color="white" label="First Name"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6>
                            <v-text-field box v-model="filterLastName" color="white" label="Last Name"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6>
                            <v-text-field box v-model="filterPhone" color="white" label="Phone"></v-text-field>
                        </v-flex>
                        <v-flex xs12 sm6>
                            <v-layout align-right column wrap>
                                <v-flex xs6 sm3>
                                    <v-switch v-model="filterMemberOnly" color="white" label="Members only"></v-switch>
                                    <v-switch v-model="filterExpireIn30Days" color="white" label="Membership expire within 30 days"></v-switch>
                                </v-flex>
                            </v-layout>
                        </v-flex>

                    </v-layout>
                </v-container>
            </v-card>
        </v-bottom-sheet>

    </v-app>
</template>

<script>
// import _ from 'lodash';
import axios from 'axios';
import { format, parse, distanceInWordsToNow, differenceInCalendarDays } from "date-fns";
import Membership from "../components/Membership"
import Expiry from "../components/Expiry"

export default {
    name: "Home",
    components: {Membership,Expiry},
    data: () => ({
        cachedEntries:[],
        filterOn:false,
        filterIndicator:{ false : 'teal lighten-1', true: 'pink lighten-1'},
        filterFirstName: '',
        filterLastName: '',
        filterPhone: '',
        filterMemberOnly: false,
        filterExpireIn30Days: false,
        logo: 'data:image/png;base64,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',
        showFilters: false,
        pagination:{
            rowsPerPage: 10
        },
        extended: false,

        expanded:false,
        descriptionLimit: 60,
        entries: [],
        isLoading: false,
        selectedPerson: null,
        search: null,
        select:null,
        tableSearch: '',

        headers: [
            { text: 'ID', value: 'id'},
            { text: 'First Name', value: 'first_name'},
            { text: 'Last Name', value: 'last_name' },
            { text: 'Phone', value: 'phone' },
            { text: 'Membership', value: 'membership.name' },
            { text: 'Expiry', value: 'membership.expiry' }
            ],
    }),

    computed: {
      isFilterOn(){
        //   this.filterOn = this.filterFirstName == '' || this.filterLastName == '' || this.filterPhone == ''
          return this.filterOn ? 'ON': 'OFF';
      },
      items () {
        return this.entries.map(entry => {
          const searchable = `${entry.first_name} ${entry.last_name} ${entry.phone}`
          let datesLeft = -1;
          if (entry.membership != null ){
            datesLeft = differenceInCalendarDays(parse(entry.membership.expiry), new Date() );
          }
          return Object.assign({}, entry, { searchable, datesLeft })
        })
      }
    },


    watch: {
        selectedPerson(val){
            console.log(val);
            if( val instanceof Object) {
                this.cachedEntries=this.entries;
                this.entries=[val];
            }
        },
        async filterFirstName(val){
            console.log(val);
            this.filterOn=(val.length!=0);
            console.log(this.filterOn);
            await this.query('');
        },
        async filterLastName(val){
            this.filterOn=(val.length!=0);
            await this.query('');
        },
        async filterPhone(val){
            this.filterOn=(val.length!=0);
            await this.query('');
        },
        async filterMemberOnly(val){
            this.filterOn=val;
            // if query changed query again
            await this.query('');

            this.entries=this.entries.filter(x=>val==(x.membership!=null));
        },
        async filterExpireIn30Days(val){
            this.filterOn=val;
            await this.query('');

            this.entries=this.entries.filter(x=>{
                    let datesLeft = -1;
                    if (x.membership != null ){
                        datesLeft = parseInt(differenceInCalendarDays(parse(x.membership.expiry), new Date() ));
                    }

                    console.log( val==(-1<datesLeft && datesLeft<=30) )
                    return val==(-1<datesLeft && datesLeft<=30)
                });
        },
        search (val) {
            val && val !== this.selectedPerson && this.query(val)
        }
    },

    methods: {
        clear () {
            // if cachedEntries is not empty
            this.entries = this.cachedEntries; //_.map(this.cachedEntries, _.clone);
            console.log(this.entries);
            this.selectedPerson='';
        },
        query (val=null) {
            // build the query paramaters
            this.isLoading = true

            // Lazily load input items
            let params = {
                params: {
                    first_name: val
                }
            };
            if(this.filterFirstName!=""){
                params.params.first_name=this.filterFirstName
            }
            if(this.filterLastName!=""){
                params.params.last_name=this.filterLastName
            }
            if(this.filterPhone!=""){
                params.params.phone=this.filterPhone
            }
            if( val == "" && !this.filterOn ){
                params = null
            }
            return axios.get('/api/search',params)
            .then(response => {
                this.entries = response.data;
            })
            .catch(err => {
                console.log(err)
            })
            .finally(() => (this.isLoading = false))
        }
    },
    mounted() {
        // fetch all the clients
        this.query();
    }
  }
</script>

<style lang="css" scoped>
.logo {
  font-weight: 400;
  padding-top: 4px;
  font-size: 25px;
  color: rgb(74, 164, 114);
  font-style: italic;
}
</style>