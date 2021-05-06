<template>
  <div>
    <AppBar backRoute="home" />

    <v-col cols="12" lg="11" class="mx-auto">

      <div class="text-h5 py-4">Purchases</div>

      <v-row align="end">
        <v-col cols="12" sm="12" md="6" class="text-h5 d-flex">
          <v-btn color="indigo" dark :to="{ name: 'purchases.action' }">
            <v-icon class="mr-1 subtitle-1">mdi-plus</v-icon>
            new purchase
          </v-btn>

          <div class="grey--text text--lighten-1 mx-4 font-weight-thin" style="font-size: 1.5rem">|</div>

          <v-btn :color="$vuetify.theme.dark ? '' : 'white purple--text'" @click="refreshTable()"
            :loading="refreshLoading" :disabled="records.length == 0">
              <v-icon class="mr-2">mdi-table-refresh</v-icon>
              refresh
          </v-btn>

          <!-- Column Menu -->
          <template>
            <div class="text-center">
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark v-bind="attrs" v-on="on" class="ml-2"
                    :color="$vuetify.theme.dark ? '' : 'white purple--text'">
                      <v-icon class="mr-2">mdi-table-eye</v-icon>
                      Columns <v-icon class="ml-2">mdi-menu-down</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item v-for="(col, index) in extraColumns" :key="index">
                    <v-checkbox v-model="selectedColumns" :label="col.label" :value="col.value"></v-checkbox>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>
          </template>
        </v-col>

        <!-- Search -->
        <v-col cols="12" sm="12" md="3" offset-md="3"
          class="d-flex justify-end">
            <v-text-field
              id="searchInput"
              solo
              dense
              clearable
              hide-details
              v-model="query"
              label="Search records"
              :loading="searchLoading"
              :disabled="searchLoading"
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
                    <span class="sort-link" @click="sortRecords('date')">Date</span>
                    <span v-if="sortBy == 'date'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'fromName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'fromName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('fromName')">From account</span>
                    <span v-if="sortBy == 'fromName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'toName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'toName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('toName')">To godown</span>
                    <span v-if="sortBy == 'toName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'productLotNumber' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'productLotNumber' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('productLotNumber')">Lot number</span>
                    <span v-if="sortBy == 'productLotNumber'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'productName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'productName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('productName')">Product</span>
                    <span v-if="sortBy == 'productName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-right" :class="sortBy == 'quantity' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'quantity' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('quantity')">Quantity</span>
                    <span v-if="sortBy == 'quantity'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'unit' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'unit' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('unit')">Unit</span>
                    <span v-if="sortBy == 'unit'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th v-if="selectedColumns.indexOf('remarks') >= 0"
                  class="subtitle-2" :class="sortBy == 'remarks' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'remarks' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('remarks')">Remarks</span>
                    <span v-if="sortBy == 'remarks'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2" :class="sortBy == 'updated_at' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'updated_at' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('updated_at')">Last modified on</span>
                    <span v-if="sortBy == 'updated_at'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th v-if="selectedColumns.indexOf('created_at') >= 0"
                  class="subtitle-2" :class="sortBy == 'created_at' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'created_at' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('created_at')">Created at</span>
                    <span v-if="sortBy == 'created_at'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

              </tr>
            </thead>

            <tbody v-if="records.length > 0">
              <tr v-for="record in records" :key="record.name" style="cursor: pointer"
                @click="viewRecordDialog = true; loadRecord(record.id)">
                  <td class="subtitle-1 text-center font-weight-bold">
                    {{ record.date | moment('DD/MM/YYYY') }}
                  </td>

                  <td class="subtitle-1">{{ record.fromName }}</td>

                  <td class="subtitle-1">{{ record.toName }}</td>

                  <td class="subtitle-1 text-center grey--text font-weight-bold"
                    :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                      <span v-if="record.productLotNumber">{{ record.productLotNumber }}</span>
                      <span v-else>-</span>
                  </td>

                  <td class="subtitle-1">{{ record.productName }}</td>

                  <td class="subtitle-1 text-right font-weight-bold">{{ formatQuantity(record.quantity) }}</td>

                  <td class="subtitle-2 grey--text"
                    :class="$vuetify.theme.dark ? '' : 'text--darken-1'">
                      {{ record.productUnit }}
                  </td>

                  <td v-if="selectedColumns.indexOf('remarks') >= 0" class="subtitle-1">
                    {{ record.remarks }}
                  </td>

                  <td class="subtitle-1 grey--text">{{ record.updated_at | moment('dddd, DD/MM/YYYY') }}</td>

                  <td v-if="selectedColumns.indexOf('created_at') >= 0" class="subtitle-1">
                    {{ record.created_at | moment('dddd, DD/MM/YYYY') }}
                  </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td :colspan="8 + selectedColumns.length" class="text-center subtitle-1">No records found.</td>
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
          <div>Purchase Details</div>
          <v-btn icon @click="viewRecordDialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">
          <v-row>
            <v-col cols="12" md="7" class="pr-10">
              <div class="overline pl-2 grey--text mb-2" style="font-size: 0.9rem !important">Transfer details</div>
              <table class="view-record-table">
                <tr>
                  <td class="subtitle-1 font-weight-bold text-left" style="width: 25%">Purchase date</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.date | moment('DD/MM/YYYY dddd') }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">From account</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.fromName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">To godown</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.toName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Lot number</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.productLotNumber ? record.productLotNumber : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Product</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.productName }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Quantity</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ formatQuantity(record.quantity) }} {{ record.productUnit }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Modified on</td>
                  <td class="subtitle-1">{{ record.updated_at | moment('dddd, DD/MM/YYYY - LTS') }}</td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Created on</td>
                  <td class="subtitle-1">{{ record.created_at | moment('dddd, DD/MM/YYYY - LTS') }}</td>
                </tr>
              </table>
            </v-col>

            <v-col cols="12" md="5">
              <div class="overline pl-2 grey--text mb-2" style="font-size: 0.9rem !important">Additional details</div>
              <table class="view-record-table">
                <tr>
                  <td class="subtitle-1 font-weight-bold text-left" style="width: 35%">Agent</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.agentName ? record.agentName : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Order no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.order_no ? record.order_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Invoice no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.invoice_no ? record.invoice_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Eway bill no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.eway_bill_no ? record.eway_bill_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Delivery slip no</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.delivery_slip_no ? record.delivery_slip_no : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Transport details</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.transport_details ? record.transport_details : '-' }}
                  </td>
                </tr>

                <tr>
                  <td class="subtitle-1 font-weight-bold text-left">Additional remarks</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                    {{ record.remarks ? record.remarks : '-' }}
                  </td>
                </tr>
              </table>
            </v-col>
          </v-row>
        </v-card-text>

        <v-card-actions class="d-flex justify-space-between">
          <v-btn :color="$vuetify.theme.dark ? 'primary' : 'indigo'" text
            @click="editFromTable({ name: 'purchases.action', params: { id: record.id } })">
              <v-icon class="text-h6 mr-2">mdi-circle-edit-outline</v-icon> edit purchase
          </v-btn>

          <v-btn color="error" dark text tabindex="-1" :loading="deleteButtonLoading"
            @click="deleteFromTable(record.id, { dialog: 'viewRecordDialog' })">
            <v-icon class="text-h6 mr-2">mdi-delete-outline</v-icon> delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog>

  </div>
</template>

<script>
import { CommonMixin } from '../../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  mounted() {
    this.apiRoute = 'purchases'

    this.loadRecords()

    this.setupExtraColumns([
      { value: 'remarks', label: 'Remarks' },
      { value: 'created_at', label: 'Created at' },
    ])
  },

  components: {
    AppBar: require('../../../components/AppBar').default
  }
}
</script>
