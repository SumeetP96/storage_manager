<template>
  <div>
    <AppBar backRoute="reports.index" />

    <v-col cols="12" lg="11" class="mx-auto">

      <div class="py-4 text-h5">Agent Transfers</div>

      <v-row align="start">
        <v-col cols="12" md="8" class="text-h5 d-flex align-start">
          <div style="width: 100%">
            <v-autocomplete
              autofocus
              v-model="agentId"
              hide-details
              clearable
              outlined
              placeholder="Select agent"
              auto-select-first
              @input="fetchItemRecords()"
              :items="agents"
              item-text="name"
              item-value="id"
              prepend-inner-icon="mdi-account-supervisor-circle-outline"
              class="left-input"
              :class="$vuetify.theme.dark ? '' : 'white'"
              dense>
            </v-autocomplete>
            <div v-if="accountDetails.remarks"
              :class="$vuetify.theme.dark ? '' : 'white'" class="subtitle-2 px-3 py-1 rounded">
                {{ accountDetails.remarks }}
            </div>
          </div>

          <v-text-field
            v-model="record.fromDate"
            hide-details="auto"
            outlined
            placeholder="From date"
            @blur="formatDate('fromDate'); dbFromDate = flipToYMD(record.fromDate); fetchDateRecords()"
            :disabled="!agentId"
            prepend-inner-icon="mdi-calendar"
            :class="$vuetify.theme.dark ? '' : 'white'"
            class="center-input ml-2"
            dense>
          </v-text-field>

          <v-text-field
            v-model="record.toDate"
            hide-details="auto"
            outlined
            placeholder="To date"
            @blur="formatDate('toDate'); dbToDate = flipToYMD(record.toDate); fetchDateRecords()"
            :disabled="!agentId"
            prepend-inner-icon="mdi-calendar"
            :class="$vuetify.theme.dark ? '' : 'white'"
            class="center-input ml-2"
            dense>
          </v-text-field>

        </v-col>

        <!-- Search -->
        <v-col cols="12" md="4" class="d-flex justify-end align-center">
            <v-btn class="mr-2" :color="$vuetify.theme.dark ? '' : 'white purple--text'"
              @click="resetDates(); refreshTable('date');" :loading="refreshLoading" :disabled="records.length == 0">
                <v-icon class="mr-2">mdi-table-refresh</v-icon>
                refresh
            </v-btn>

            <v-text-field
              id="searchInput"
              solo
              dense
              clearable
              hide-details
              v-model="query"
              label="Search records"
              :loading="searchLoading"
              :disabled="searchLoading || !agentId"
              @click:clear="clearSearch()"
              @keypress.enter="searchRecords()"
              prepend-inner-icon="mdi-magnify">
            </v-text-field>
        </v-col>
      </v-row>

      <div class="mt-3">
        <v-skeleton-loader v-bind="attrs"
          v-if="recordsTableLoading"
          type="table-row-divider@6"
          :class="$vuetify.theme.dark ? '' : 'white'" class="px-4">
        </v-skeleton-loader>

        <v-simple-table v-else class="elevation-1">
          <template v-slot:default>
            <thead>
              <tr>
                <th class="subtitle-2 text-center" :class="sortBy == 'date' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'date' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('date', 'date')">Date</span>
                    <span v-if="sortBy == 'date'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'transferType' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'transferType' ? 'font-size: 1rem !important' : ''" style="width: 150px">
                    <span class="sort-link" @click="sortRecords('transferType', 'date')">Transfer type</span>
                    <span v-if="sortBy == 'transferType'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'fromName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'fromName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('fromName', 'date')">From</span>
                    <span v-if="sortBy == 'fromName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'toName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'toName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('toName', 'date')">To</span>
                    <span v-if="sortBy == 'toName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'lotNumber' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'lotNumber' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('lotNumber', 'date')">Lot number</span>
                    <span v-if="sortBy == 'lotNumber'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'productName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'productName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('productName', 'date')">Product</span>
                    <span v-if="sortBy == 'productName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-right" :class="sortBy == 'quantity' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'quantity' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('quantity', 'date')">Quantity</span>
                    <span v-if="sortBy == 'quantity'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'unit' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'unit' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('unit', 'date')">Unit</span>
                    <span v-if="sortBy == 'unit'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

              </tr>
            </thead>
            <tbody v-if="records.length > 0">
              <tr v-for="record in records" :key="record.id">
                  <td class="subtitle-1 text-center font-weight-bold">{{ record.date | moment('DD/MM/YYYY') }}</td>

                  <td class="subtitle-1 text-center">
                    <v-btn @click="viewRecordDialog = true; loadRecordDialog(record.id, record.ttid)"
                      v-if="record.ttid == 1" text width="100%"
                      :color="$vuetify.theme.dark ? 'primary' : 'indigo lighten-1'">
                        {{ record.transferType }}
                    </v-btn>

                    <v-btn @click="viewRecordDialog = true; loadRecordDialog(record.id, record.ttid)"
                      v-if="record.ttid == 2" text width="100%"
                      :color="$vuetify.theme.dark ? 'orange' : 'orange darken-2'">
                        {{ record.transferType }}
                    </v-btn>

                    <v-btn @click="viewRecordDialog = true; loadRecordDialog(record.id, record.ttid)"
                      v-if="record.ttid == 3" text width="100%" color="success">
                      {{ record.transferType }}
                    </v-btn>
                  </td>

                  <td class="subtitle-1">{{ record.fromName }}</td>
                  <td class="subtitle-1">{{ record.toName }}</td>

                  <td class="subtitle-1 text-center">{{ record.lotNumber }}</td>
                  <td class="subtitle-1">{{ record.productName }}</td>

                  <td class="subtitle-1 text-right font-weight-bold">
                    <span v-if="record.ttid == 1">{{ formatQuantity(record.quantity) }}</span>
                    <span v-if="record.ttid == 2" class="success--text">
                      + {{ formatQuantity(record.quantity) }}
                    </span>
                    <span v-if="record.ttid == 3" class="error--text">
                      - {{ formatQuantity(record.quantity) }}
                    </span>
                  </td>

                  <td class="subtitle-2 grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                    {{ record.unit }}
                  </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td :colspan="8" class="text-center subtitle-1">No records found.</td>
              </tr>
            </tbody>
          </template>
        </v-simple-table>
        <v-skeleton-loader v-bind="attrs" v-if="addRecordsTableLoading" type="table-row-divider@3"
          :class="$vuetify.theme.dark ? '' : 'white'" class="px-4">
        </v-skeleton-loader>

        <div class="d-flex justify-space-between mt-3 ml-3">
          <div v-if="records">
            ( {{ records.length }} of {{ totalRecords }} )
          </div>
          <div class="d-flex justify-end align-center">
            Records per page
            <v-select
              @change="loadRecords()"
              v-model="recordsPerPage"
              :items="perPageItems"
              hide-details
              dense solo
              style="width: 85px"
              class="ml-3 elevation-0 center-input">
            </v-select>

            <v-btn color="indigo white--text" class="ml-5"
              :loading="loadMoreLoading"
              :disabled="totalRecords == records.length"
              @click="loadRecords({ loader: 'addRecordsTableLoading', loadMore: true, reset: false })">
              load more
              <v-icon>mdi-chevron-down</v-icon>
            </v-btn>
          </div>
        </div>
      </div>

    </v-col>

    <v-dialog v-model="viewRecordDialog" max-width="1200">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>{{ recordType }} Details</div>
          <v-btn icon @click="viewRecordDialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">
          <v-row>
            <v-col cols="12" md="7" class="pr-10">
              <div class="overline pl-2 grey--text mb-2" style="font-size: 0.9rem !important">Transfer details</div>
              <table class="view-record-table">
                <tr>
                  <td class="subtitle-1 font-weight-bold text-left" style="width: 25%">{{ recordType }} date</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.date | moment('DD/MM/YYYY dddd') }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">From account</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.fromName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">To godown</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.toName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Lot number</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.productLotNumber ? dbRecord.productLotNumber : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Product</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.productName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Quantity</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ formatQuantity(dbRecord.quantity) }} {{ dbRecord.productUnit }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Modified on</td>
                  <td class="subtitle-1">{{ dbRecord.updated_at | moment('dddd, DD/MM/YYYY - LTS') }}</td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Created on</td>
                  <td class="subtitle-1">{{ dbRecord.created_at | moment('dddd, DD/MM/YYYY - LTS') }}</td>
                </tr>
              </table>
            </v-col>

            <v-col cols="12" md="5">
              <div class="overline pl-2 grey--text mb-2" style="font-size: 0.9rem !important">Additional details</div>
              <table class="view-record-table">
                <tr>
                  <td class="subtitle-1 font-weight-bold text-left" style="width: 35%">Agent</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.agentName ? dbRecord.agentName : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Order no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.order_no ? dbRecord.order_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Invoice no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.invoice_no ? dbRecord.invoice_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Eway bill no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.eway_bill_no ? dbRecord.eway_bill_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Delivery slip no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.delivery_slip_no ? dbRecord.delivery_slip_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Transport details</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.transport_details ? dbRecord.transport_details : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Additional remarks</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ dbRecord.remarks ? dbRecord.remarks : '-' }}
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>
        </v-card-text>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
import { CommonMixin } from '../../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  data() {
    return {
      record: {
        fromDate: '',
        toDate: ''
      },

      dbRecord: {},

      lastFrom: '',
      lastTo: '',

      dbFromDate: '',
      dbToDate: '',

      recordType: '',

      agentId: '',
      agents: [],
      accountDetails: {},
    }
  },

  mounted() {
    this.apiRoute = 'reports/agent_transfers'
    this.customQuery = `agent_id=${this.agentId}`
    this.sortBy = 'date'

    this.axios.get('/api/agents/autocomplete')
      .then(response => this.agents = response.data.records)

    if (this.agents) this.loadRecords()
  },

  components: {
    AppBar: require('../../../components/AppBar').default
  },

  methods: {
    fetchItemRecords() {
      this.axios.get(`/api/agents/${this.agentId}/details`)
        .then(response => this.accountDetails = response.data)

      this.customQuery = `agent_id=${this.agentId}`
      this.loadRecords()
    },

    fetchDateRecords() {
      if (this.dbFromDate == '' && this.dbToDate == '') {
        if (this.dbFromDate == this.lastFrom && this.dbToDate == this.lastTo) return
        this.fetchItemRecords()
        this.lastFrom = ''
        this.lastTo = ''
      }

      if (this.dbFromDate != '' && this.dbToDate != '') {
        if (this.dbFromDate == this.lastFrom && this.dbToDate == this.lastTo) return

        this.customQuery = `agent_id=${this.agentId}&from=${this.dbFromDate}&to=${this.dbToDate}`
        this.loadRecords()
        this.lastFrom = this.dbFromDate
        this.lastTo = this.dbToDate
      }
    },

    resetDates() {
      this.record.fromDate = ''
      this.record.toDate = ''
      this.dbFromDate = ''
      this.dbToDate = ''
      this.customQuery = `agent_id=${this.agentId}`
    },

    loadRecordDialog(agentId, transferTypeId) {
      let apiRoute = ''

      this.dbRecord = {}

      if (transferTypeId == 1) {
        this.recordType = 'Inter Godown'
        apiRoute = 'inter_godowns'
      }
      if (transferTypeId == 2) {
        this.recordType = 'Purchase'
        apiRoute = 'purchases'
      }
      if (transferTypeId == 3) {
        this.recordType = 'Sale'
        apiRoute = 'sales'
      }

      this.axios
        .get(`/api/${apiRoute}/${agentId}/show`)
        .then(response => this.dbRecord = response.data.record)
    }
  }
}
</script>

<style scoped>
  .right-input >>> input {
    text-align: right
  }

  .center-input >>> input {
    text-align: center;
    padding-left: 2px;
  }

  .left-input >>> input {
    padding-left: 10px;
  }
</style>
