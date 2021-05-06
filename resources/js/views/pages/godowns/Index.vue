<template>
  <div>
    <AppBar backRoute="home" />

    <v-col cols="12" lg="11" class="mx-auto">

      <div class="text-h5 py-4">Godowns</div>

      <v-row align="end">
        <v-col cols="12" sm="12" md="6" class="text-h5 d-flex">
          <v-btn color="indigo" dark :to="{ name: 'godowns.action' }">
            <v-icon class="mr-1 subtitle-1">mdi-plus</v-icon>
            new godown
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
                <th class="subtitle-2" :class="sortBy == 'name' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'name' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('name')">Name</span>
                    <span v-if="sortBy == 'name'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'alias' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'alias' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('alias')">Alias</span>
                    <span v-if="sortBy == 'alias'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'contact_1' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'contact_1' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('contact_1')">Contact 1</span>
                    <span v-if="sortBy == 'contact_1'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th class="subtitle-2 text-center" :class="sortBy == 'contact_2' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'contact_2' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('contact_2')">Contact 2</span>
                    <span v-if="sortBy == 'contact_2'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <th v-if="selectedColumns.indexOf('email') >= 0"
                  class="subtitle-2" :class="sortBy == 'email' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'email' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('email')">Email</span>
                    <span v-if="sortBy == 'email'">
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
                  <td class="subtitle-1">{{ record.name }}</td>

                  <td class="subtitle-1 text-center">{{ record.alias }}</td>

                  <td class="subtitle-1 text-center">
                    <span v-if="record.contact_1">{{ record.contact_1 }}</span>
                    <span v-else>-</span>
                  </td>

                  <td class="subtitle-1 text-center">
                    <span v-if="record.contact_2">{{ record.contact_2 }}</span>
                    <span v-else>-</span>
                  </td>

                  <td class="subtitle-1" v-if="selectedColumns.indexOf('email') >= 0">
                    <span v-if="record.email">{{ record.email }}</span>
                    <span v-else>-</span>
                  </td>

                  <td v-if="selectedColumns.indexOf('remarks') >= 0" class="subtitle-1">{{ record.remarks }}</td>

                  <td class="subtitle-1 grey--text">{{ record.updated_at | moment('dddd, DD/MM/YYYY') }}</td>

                  <td v-if="selectedColumns.indexOf('created_at') >= 0" class="subtitle-1">
                    {{ record.created_at | moment('dddd, DD/MM/YYYY') }}
                  </td>
              </tr>
            </tbody>
            <tbody v-else>
              <tr>
                <td :colspan="5 + selectedColumns.length" class="text-center subtitle-1">No records found.</td>
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

    <v-dialog v-model="viewRecordDialog" max-width="800">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>Godown Details</div>
          <v-btn icon @click="viewRecordDialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">
          <table class="view-record-table">
            <tr>
              <td class="subtitle-1 font-weight-bold text-left" style="width: 20%">Name</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ record.name }}
              </td>
            </tr>

            <tr>
              <td class="subtitle-1 font-weight-bold text-left">Alias</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ record.alias ? record.alias : '-' }}
              </td>
            </tr>

            <tr>
              <td class="subtitle-1 font-weight-bold text-left">Contact 1</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ record.contact_1 ? record.contact_1 : '-' }}
              </td>
            </tr>

            <tr>
              <td class="subtitle-1 font-weight-bold text-left">Contact 2</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ record.contact_2 ? record.contact_2 : '-' }}
              </td>
            </tr>

            <tr>
              <td class="subtitle-1 font-weight-bold text-left">Email</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ record.email ? record.email : '-' }}
              </td>
            </tr>

            <tr>
              <td class="subtitle-1 font-weight-bold text-left">Remarks</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ record.remarks ? record.remarks : '-' }}
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
        </v-card-text>

        <v-card-actions class="d-flex justify-space-between">
          <v-btn :color="$vuetify.theme.dark ? 'primary' : 'indigo'" text
            @click="editFromTable({ name: 'godowns.action', params: { id: record.id } })">
              <v-icon class="text-h6 mr-2">mdi-circle-edit-outline</v-icon> edit godown
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
import { CommonMixin } from '../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  mounted() {
    this.apiRoute = 'godowns'
    this.customQuery = 'is_account=0'

    this.loadRecords()

    this.setupExtraColumns([
      { value: 'email', label: 'Email' },
      { value: 'remarks', label: 'Remarks' },
      { value: 'created_at', label: 'Created at' }
    ])
  },

  components: {
    AppBar: require('../../components/AppBar').default
  }
}
</script>
