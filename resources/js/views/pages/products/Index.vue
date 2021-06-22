<template>
  <div>
    <AppBar backRoute="home" />


    <!-- Main Col Wrapper -->
    <v-col cols="12" lg="11" class="mx-auto">

      <div class="text-h5 py-4">Products</div>

      <!-- Above Table -->
      <v-row align="end">
        <!-- Left Section -->
        <v-col cols="12" sm="12" md="6" class="text-h5 d-flex">

          <!-- New record -->
          <v-btn color="indigo" dark :to="{ name: 'products.action' }"
            v-shortkey="['alt', 'n']" @shortkey.once="$router.push({ name: 'products.action' })">
              <v-icon class="mr-1 subtitle-1">mdi-plus</v-icon>
              new product
          </v-btn>

          <div class="grey--text text--lighten-1 mx-4 font-weight-thin" style="font-size: 1.5rem">|</div>

          <!-- Column Menu -->
          <template>
            <div class="text-center">
              <v-menu offset-y :close-on-content-click="false">
                <template v-slot:activator="{ on, attrs }">
                  <v-btn dark v-bind="attrs" v-on="on"
                    :color="$vuetify.theme.dark ? 'purple--text text--lighten-3' : 'white purple--text'"
                    v-shortkey="['alt', 's']" @shortkey="focusSearch()">
                      <v-icon class="mr-2">mdi-table-eye</v-icon>
                      Columns
                      <v-icon class="ml-2">mdi-menu-down</v-icon>
                  </v-btn>
                </template>
                <v-list>
                  <v-list-item v-for="(col, index) in extraColumns" :key="index">
                    <v-checkbox v-model="selectedColumns" :label="col.label" :value="col.value"></v-checkbox>
                  </v-list-item>
                </v-list>
              </v-menu>
            </div>
          </template> <!-- / Column Menu End -->

          <div class="grey--text text--lighten-1 mx-4 font-weight-thin" style="font-size: 1.5rem">|</div>

          <!-- PDF -->
          <v-btn tabindex="-1" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'error--text' : 'white error--text'"
            :href="`/exports/pdf/products?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.pdf`">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Excel -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'success--text' : 'white success--text'"
            :href="`/exports/excel/products?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.xlsx`">
              <v-icon class="text-h6 mr-2">mdi-file-excel</v-icon> excel
          </v-btn>

          <!-- Print -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px"
          :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'primary--text' : 'white indigo--text'"
            :disabled="disableExport || records.length == 0" @click="disableExportButtons();
              printPage('all-print', `/exports/print/products?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`)">
              <v-icon class="mr-2">mdi-printer</v-icon> Print
          </v-btn>
          <iframe id="all-print" style="display: none"></iframe>

        </v-col> <!-- / Left Section End -->

        <!-- Right Section -->
        <v-col cols="12" sm="12" md="4" offset-md="2" class="d-flex justify-end align-center">
          <!-- Refresh -->
          <v-btn :color="$vuetify.theme.dark ? 'purple--text text--lighten-3' : 'white purple--text'"
            @click="refreshTable()"
            class="mr-2" v-shortkey.once="['alt', 'r']" @shortkey="refreshTable()"
            :loading="refreshLoading" :disabled="records.length == 0">
              <v-icon class="mr-2">mdi-table-refresh</v-icon>
              refresh
          </v-btn>

          <!-- Search -->
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
        </v-col> <!-- / Right Section End -->
      </v-row> <!-- / Above Table End -->


      <!-- Table Section -->
      <div class="mt-3">
        <!-- Table loader -->
        <v-skeleton-loader v-bind="attrs" v-if="recordsTableLoading" type="table-row-divider@6"
          :class="$vuetify.theme.dark ? '' : 'white'" class="px-4">
        </v-skeleton-loader>

        <!-- Records Table -->
        <v-simple-table v-else class="elevation-1">
          <template v-slot:default>
            <thead>
              <tr>

                <!-- Name -->
                <th class="subtitle-2" :class="sortBy == 'name' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'name' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('name')">Name</span>
                    <span v-if="sortBy == 'name'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <!-- Alias -->
                <th class="subtitle-2" :class="sortBy == 'alias' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'alias' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('alias')">Alias</span>
                    <span v-if="sortBy == 'alias'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Alias filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="800px" min-width="250px" :value="alias_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('alias') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="alias_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>

                            <div class="rounded px-4 pb-4 mt-3"
                              :class="$vuetify.theme.dark ? 'grey darken-4' : 'white'">
                              <div class="subtitle-2 pt-3">Select records</div>
                              <v-radio-group v-model="alias" column hide-details>
                                <v-radio label="With alias" value="with"></v-radio>
                                <v-radio label="Without alias" value="without"></v-radio>
                              </v-radio-group>
                            </div>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('alias', 'withWithout')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('alias', 'withWithout')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Alias filter end -->
                </th>

                <!-- Unit -->
                <th class="subtitle-2" :class="sortBy == 'unit' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'unit' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('unit')">Unit</span>
                    <span v-if="sortBy == 'unit'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Unit filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="unit_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('unit') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="unit_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="unitSelectOnly"
                              :items="units"
                              label="Unit to show"
                              item-value="unit"
                              item-text="unit"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="unitSelectExcept.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="unitSelectExcept"
                              :items="units"
                              :disabled="unitSelectOnly.length > 0 || filterLoading"
                              :loading="filterLoading"
                              label="Unit to hide"
                              item-value="unit"
                              item-text="unit"
                              multiple
                              clearable
                              solo
                              dense
                            ></v-combobox>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('unit', 'onlyExcept')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('unit', 'onlyExcept')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Unit filter end -->
                </th>

                <!-- Packing -->
                <th class="subtitle-2" :class="sortBy == 'packing' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'packing' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('packing')">Packing</span>
                    <span v-if="sortBy == 'packing'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Packing filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="packing_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('packing') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="packing_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select greater than</div>

                            <v-text-field
                              v-model="packingGt"
                              :disabled="filterLoading"
                              :loading="filterLoading"
                              placeholder="0"
                              dense
                              solo>
                            </v-text-field>

                            <div class="subtitle-2 my-1">Select less than</div>

                            <v-text-field
                              v-model="packingLt"
                              :disabled="filterLoading"
                              :loading="filterLoading"
                              placeholder="0"
                              dense
                              solo>
                            </v-text-field>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('packing', 'lessGreat')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('packing', 'lessGreat')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Packing filter end -->
                </th>

                <!-- Remarks -->
                <th v-if="selectedColumns.indexOf('remarks') >= 0"
                  class="subtitle-2" :class="sortBy == 'remarks' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'remarks' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('remarks')">Remarks</span>
                    <span v-if="sortBy == 'remarks'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <!-- Updated at -->
                <th class="subtitle-2" :class="sortBy == 'updated_at' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'updated_at' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('updated_at')">Last modified on</span>
                    <span v-if="sortBy == 'updated_at'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Updated at filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="250px" :value="updatedRange_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('updatedAt') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="updatedRange_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Date range</div>

                            <v-text-field
                              v-model="record.updatedFromDate"
                              hide-details
                              solo
                              :loading="filterLoading"
                              :disabled="filterLoading"
                              placeholder="From date"
                              @blur="formatDate('updatedFromDate'); dbUpdatedFromDate = flipToYMD(record.updatedFromDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-3"
                              dense>
                            </v-text-field>

                            <v-text-field
                              v-model="record.updatedToDate"
                              hide-details
                              solo
                              :disabled="filterLoading"
                              :loading="filterLoading"
                              placeholder="To date"
                              @blur="formatDate('updatedToDate'); dbUpdatedToDate = flipToYMD(record.updatedToDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-2"
                              dense>
                            </v-text-field>

                            <div class="d-flex justify-space-between align-center mt-5 mb-1">
                              <v-btn dark small @click="removeFilter('updatedAt', 'updatedRange')" tabindex="-1" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('updatedAt', 'updatedRange')" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Updated at filter end -->
                </th>

                <!-- Created at -->
                <th v-if="selectedColumns.indexOf('created_at') >= 0"
                  class="subtitle-2" :class="sortBy == 'created_at' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'created_at' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('created_at')">Created at</span>
                    <span v-if="sortBy == 'created_at'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Created at filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="250px" :value="createdRange_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('createdAt') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="createdRange_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Date range</div>

                            <v-text-field
                              v-model="record.createdFromDate"
                              hide-details
                              solo
                              :loading="filterLoading"
                              :disabled="filterLoading"
                              placeholder="From date"
                              @blur="formatDate('createdFromDate'); dbCreatedFromDate = flipToYMD(record.createdFromDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-3"
                              dense>
                            </v-text-field>

                            <v-text-field
                              v-model="record.createdToDate"
                              hide-details
                              solo
                              :disabled="filterLoading"
                              :loading="filterLoading"
                              placeholder="To date"
                              @blur="formatDate('createdToDate'); dbCreatedToDate = flipToYMD(record.createdToDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-2"
                              dense>
                            </v-text-field>

                            <div class="d-flex justify-space-between align-center mt-5 mb-1">
                              <v-btn dark small @click="removeFilter('createdAt', 'createdRange')" tabindex="-1" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('createdAt', 'createdRange')" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Created at filter end -->
                </th>

              </tr>
            </thead>

            <tbody v-if="records.length > 0">
              <tr v-for="record in records" :key="record.name" style="cursor: pointer"
                @click="viewRecordDialog = true; loadRecord(record.id)">
                  <td class="subtitle-1">{{ record.name }}</td>

                  <td class="subtitle-1">{{ record.alias ? record.alias : '-' }}</td>

                  <td class="subtitle-1 font-weight-bold">{{ record.unit }}</td>

                  <td class="subtitle-1 font-weight-bold">
                    {{ formatQuantity(record.packing, 0) }} <span class="subtitle-2">KGS</span>
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
                <td :colspan="5 + selectedColumns.length" class="text-center subtitle-1">No records found.</td>
              </tr>
            </tbody>
          </template>
        </v-simple-table> <!-- / Records Table End -->

        <!-- Load more loader -->
        <v-skeleton-loader v-bind="attrs" v-if="addRecordsTableLoading" type="table-row-divider@3"
          :class="$vuetify.theme.dark ? '' : 'white'" class="px-4">
        </v-skeleton-loader>
      </div> <!-- / Table Section End -->


      <!-- Below Table -->
      <div class="d-flex justify-space-between mt-3 ml-3">
        <!-- Records Length -->
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

          <!-- Load more -->
          <v-btn color="indigo white--text" class="ml-5"
            :loading="loadMoreLoading"
            :disabled="totalRecords == records.length"
            v-shortkey.once="['alt', 'l']"
            @shortkey="loadRecords({ loader: 'addRecordsTableLoading', loadMore: true, reset: false })"
            @click="loadRecords({ loader: 'addRecordsTableLoading', loadMore: true, reset: false })">
            load more
            <v-icon>mdi-chevron-down</v-icon>
          </v-btn>
        </div>
      </div> <!-- / Below Table End -->

    </v-col>
    <!-- / Main Col Wrapper End -->


    <!-- View Record Dialog -->
    <v-dialog v-model="viewRecordDialog" max-width="800">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          Product Details
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
              <td class="subtitle-1 font-weight-bold text-left">Unit</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ record.unit }}
              </td>
            </tr>

            <tr>
              <td class="subtitle-1 font-weight-bold text-left">Packing</td>
              <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">
                {{ formatQuantity(record.packing, 0) }}
                <span class="subtitle-2">KGS</span>
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
          <div class="d-flex">
            <v-btn :color="$vuetify.theme.dark ? 'primary' : 'indigo'" text
              @click="editFromTable({ name: 'products.action', params: { id: record.id } })">
                <v-icon class="text-h6 mr-2">mdi-circle-edit-outline</v-icon> edit product
            </v-btn>

            <div class="grey--text text--lighten-1 mx-2 font-weight-thin" style="font-size: 1.5rem">|</div>

            <!-- PDF -->
            <v-btn color="error" text tabindex="-1" :disabled="disableExport"
              @click="disableExportButtons(2)"
              :href="`/exports/pdf/products/${record.id}`"
              :download="`${apiRoute}.pdf`">
                <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
            </v-btn>

            <!-- Print -->
            <v-btn @click="disableExportButtons(2); printPage('print-record', `/exports/print/products/${record.id}`)"
              text tabindex="-1" :disabled="disableExport" :color="$vuetify.theme.dark ? 'purple lighten-2' : 'purple'">
                <v-icon class="mr-2">mdi-printer</v-icon> Print
            </v-btn>
            <iframe id="print-record" style="display: none"></iframe>
          </div>

          <v-btn color="error" dark text tabindex="-1" :loading="deleteButtonLoading"
            @click="deleteFromTable(record.id, { dialog: 'viewRecordDialog' })">
            <v-icon class="text-h6 mr-2">mdi-delete-outline</v-icon> delete
          </v-btn>
        </v-card-actions>
      </v-card>
    </v-dialog> <!-- / View Record Dialog End -->

  </div>
</template>

<script>
import { CommonMixin } from '../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  mounted() {
    this.apiRoute = 'products'

    this.loadRecords()
    this.fetchUnitAutofill()

    this.setupExtraColumns([
      { value: 'remarks', label: 'Remarks' },
      { value: 'created_at', label: 'Created at' }
    ])
  },

  components: {
    AppBar: require('../../components/AppBar').default
  },

  data() {
    return {
      alias: '',
      alias_FILTER: false,

      unit_FILTER: false,
      units: [],
      unitSelectOnly: [],
      unitSelectExcept: [],

      packingLt: '',
      packingGt: '',
      packing_FILTER: false,
    }
  },

  methods: {
    fetchUnitAutofill() {
      this.filterLoading = true
      this.axios.get('/api/autofills/products/distinct_units')
        .then(response => {
          this.units = response.data
          this.filterLoading = false
        })
    }
  }
}
</script>
