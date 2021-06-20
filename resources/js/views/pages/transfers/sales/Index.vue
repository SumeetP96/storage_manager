<template>
  <div>
    <AppBar backRoute="home" />

    <!-- Main Col Wrapper -->
    <v-col cols="12" lg="11" class="mx-auto">

      <div class="text-h5 py-4">Sales</div>

      <!-- Above Table -->
      <v-row align="end">
        <!-- Left Section -->
        <v-col cols="12" sm="12" md="6" class="text-h5 d-flex">

          <!-- New record -->
          <v-btn color="indigo" dark :to="{ name: 'sales.action' }"
            v-shortkey="['alt', 'n']" @shortkey.once="$router.push({ name: 'sales.action' })">
              <v-icon class="mr-1 subtitle-1">mdi-plus</v-icon>
              new sale
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
            :href="`/exports/pdf/sales?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.pdf`">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Excel -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'success--text' : 'white success--text'"
            :href="`/exports/excel/sales?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.xlsx`">
              <v-icon class="text-h6 mr-2">mdi-file-excel</v-icon> excel
          </v-btn>

          <!-- Print -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px"
            :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'primary--text' : 'white indigo--text'"
            :disabled="disableExport || records.length == 0" @click="disableExportButtons();
              printPage('all-print', `/exports/print/sales?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`)">
              <v-icon class="mr-2">mdi-printer</v-icon> Print
          </v-btn>
          <iframe id="all-print" style="display: none"></iframe>

        </v-col> <!-- / Left Section End -->

        <!-- Right Section -->
        <v-col cols="12" sm="12" md="4" offset-md="2" class="d-flex justify-end align-center">
          <!-- Refresh -->
          <v-btn :color="$vuetify.theme.dark ? 'purple--text text--lighten-3' : 'white purple--text'"
            @click="refreshTable(sortBy)"
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

                <!-- Date -->
                <th class="subtitle-2 text-center" :class="sortBy == 'date' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'date' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('date', sortBy)">Date</span>
                    <span v-if="sortBy == 'date'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Date filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="250px" :value="date_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('date') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="date_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Date range</div>

                            <v-text-field
                              v-model="record.fromDate"
                              hide-details
                              solo
                              :loading="filterLoading"
                              :disabled="filterLoading"
                              placeholder="From date"
                              @blur="formatDate('fromDate'); dbFromDate = flipToYMD(record.fromDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-3"
                              dense>
                            </v-text-field>

                            <v-text-field
                              v-model="record.toDate"
                              hide-details
                              solo
                              :disabled="filterLoading"
                              :loading="filterLoading"
                              placeholder="To date"
                              @blur="formatDate('toDate'); dbToDate = flipToYMD(record.toDate);"
                              prepend-inner-icon="mdi-calendar"
                              :class="$vuetify.theme.dark ? '' : 'white'"
                              class="center-input mt-2"
                              dense>
                            </v-text-field>

                            <div class="d-flex justify-space-between align-center mt-5 mb-1">
                              <v-btn dark small @click="removeFilter('date', 'dateRange')" tabindex="-1" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('date', 'dateRange')" :loading="filterLoading">
                                <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Date filter end -->
                </th>

                <!-- Sale no -->
                <th class="subtitle-2" :class="sortBy == 'sale_no' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'sale_no' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('sale_no', sortBy)">Sale no</span>
                    <span v-if="sortBy == 'sale_no'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <!-- Invoice No -->
                <th class="subtitle-2" :class="sortBy == 'invoiceNo' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'invoiceNo' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('invoiceNo', sortBy)">Invoice no</span>
                    <span v-if="sortBy == 'invoiceNo'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Invoice no filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="800px" min-width="250px" :value="invoiceNo_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('invoiceNo') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="invoiceNo_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>

                            <div class="rounded px-4 pb-4 mt-3"
                              :class="$vuetify.theme.dark ? 'grey darken-4' : 'white'">
                              <div class="subtitle-2 pt-3">Select records</div>
                              <v-radio-group v-model="invoiceNo" column hide-details>
                                <v-radio label="With invoice number" value="with"></v-radio>
                                <v-radio label="Without invoice number" value="without"></v-radio>
                              </v-radio-group>
                            </div>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('invoiceNo', 'withWithout')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('invoiceNo', 'withWithout')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Invoice no filter end -->
                </th>

                <!-- From Account -->
                <th class="subtitle-2" :class="sortBy == 'fromName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'fromName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('fromName', sortBy)">From godown</span>
                    <span v-if="sortBy == 'fromName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- From Account filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="fromGodown_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('fromGodown') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="fromGodown_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="fromGodownSelectOnlyId"
                              :items="fromGodowns"
                              label="Accounts to show"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="fromGodownSelectExceptId.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="fromGodownSelectExceptId"
                              :items="fromGodowns"
                              :disabled="fromGodownSelectOnlyId.length > 0 || filterLoading"
                              :loading="filterLoading"
                              label="Accounts to hide"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              solo
                              dense
                            ></v-combobox>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('fromGodown', 'onlyExceptId')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('fromGodown', 'onlyExceptId')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / From Account filter end -->
                </th>

                <!-- To Godown -->
                <th class="subtitle-2" :class="sortBy == 'toName' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'toName' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('toName', sortBy)">To account</span>
                    <span v-if="sortBy == 'toName'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- To Godown filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="toGodown_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('toGodown') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="toGodown_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="toGodownSelectOnlyId"
                              :items="toGodowns"
                              label="Godowns to show"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="toGodownSelectExceptId.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="toGodownSelectExceptId"
                              :items="toGodowns"
                              :disabled="toGodownSelectOnlyId.length > 0 || filterLoading"
                              :loading="filterLoading"
                              label="Godowns to hide"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              solo
                              dense
                            ></v-combobox>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('toGodown', 'onlyExceptId')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('toGodown', 'onlyExceptId')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / To Godown filter end -->
                </th>

                <!-- Agent -->
                <th v-if="selectedColumns.indexOf('agent') >= 0"
                  class="subtitle-2" :class="sortBy == 'agent' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'agent' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('agent', sortBy)">Agent</span>
                    <span v-if="sortBy == 'agent'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>

                    <!-- Agent filter -->
                    <v-menu offset-y :close-on-content-click="false" max-width="300px" :value="agent_FILTER">
                      <template v-slot:activator="{ on, attrs }">
                        <v-btn :color="activeFilters.indexOf('agent') >= 0 ? 'primary' : 'grey'"
                          icon v-bind="attrs" v-on="on" @click="agent_FILTER = true">
                            <v-icon class="subtitle-2">mdi-filter-menu</v-icon>
                        </v-btn>
                      </template>
                      <v-list :class="$vuetify.theme.dark ? 'grey darken-3' : 'blue-grey lighten-4'">
                        <v-list-item>
                          <v-list-item-title>
                            <div class="subtitle-2 my-1">Select only</div>

                            <v-combobox v-model="agentSelectOnlyId"
                              :items="agents"
                              label="Agents to show"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              :loading="filterLoading"
                              :disabled="agentSelectExceptId.length > 0 || filterLoading"
                              solo
                              dense
                              class="mt-2"
                            ></v-combobox>

                            <div class="subtitle-2 my-1">Select except</div>

                            <v-combobox v-model="agentSelectExceptId"
                              :items="agents"
                              :disabled="agentSelectOnlyId.length > 0 || filterLoading"
                              :loading="filterLoading"
                              label="Agents to hide"
                              item-value="id"
                              item-text="name"
                              multiple
                              clearable
                              solo
                              dense
                            ></v-combobox>

                            <div class="d-flex justify-space-between align-center mt-3 mb-1">
                              <v-btn dark small @click="removeFilter('agent', 'onlyExceptId')"
                                tabindex="-1" :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-cancel</v-icon>
                                  clear
                              </v-btn>

                              <v-btn color="success" dark small @click="addFilter('agent', 'onlyExceptId')"
                                :loading="filterLoading">
                                  <v-icon class="subtitle-1 mr-2">mdi-filter</v-icon>
                                  filter
                              </v-btn>
                            </div>
                          </v-list-item-title>
                        </v-list-item>
                      </v-list>
                    </v-menu> <!-- / Agent filter end -->
                </th>

                <!-- Remarks -->
                <th v-if="selectedColumns.indexOf('remarks') >= 0"
                  class="subtitle-2" :class="sortBy == 'remarks' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'remarks' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('remarks', sortBy)">Remarks</span>
                    <span v-if="sortBy == 'remarks'">
                      <span v-if="flow =='asc'"><v-icon class="subtitle-1 pink--text">mdi-arrow-down</v-icon></span>
                      <span v-else><v-icon class="subtitle-1 pink--text">mdi-arrow-up</v-icon></span>
                    </span>
                </th>

                <!-- Updated at -->
                <th class="subtitle-2" :class="sortBy == 'updated_at' ? 'pink--text font-weight-bold' : ''"
                  :style="sortBy == 'updated_at' ? 'font-size: 1rem !important' : ''">
                    <span class="sort-link" @click="sortRecords('updated_at', sortBy)">Last modified on</span>
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
                    <span class="sort-link" @click="sortRecords('created_at', sortBy)">Created at</span>
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
                @click="viewRecordDialog = true; customFetchTransfer(record.id)">
                  <td class="subtitle-1 text-center font-weight-bold">
                    {{ record.date | moment('DD/MM/YYYY') }}
                  </td>

                  <td class="subtitle-1 grey--text font-weight-bold" :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                    {{ record.sale_no }}
                  </td>

                  <td class="subtitle-1"
                    :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                      {{ record.invoiceNo ? record.invoiceNo : '-' }}
                  </td>

                  <td class="subtitle-1">{{ record.fromName }}</td>

                  <td class="subtitle-1">{{ record.toName }}</td>

                  <td v-if="selectedColumns.indexOf('agent') >= 0" class="subtitle-1">
                    {{ record.agent }}
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
        </v-simple-table> <!-- / Records Table End -->

        <!-- Load more loader -->
        <v-skeleton-loader v-bind="attrs" v-if="addRecordsTableLoading" type="table-row-divider@3"
          :class="$vuetify.theme.dark ? '' : 'white'" class="px-4">
        </v-skeleton-loader>

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
      </div> <!-- / Table Section End -->

    </v-col> <!-- / Main Col Wrapper End -->


    <!-- View Record Dialog -->
    <v-dialog v-model="viewRecordDialog" max-width="90%">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>Purchase Details</div>
          <v-btn icon @click="viewRecordDialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <div class="px-6 pt-4 pb-6">
          <!-- Details -->
          <v-row no-gutters>
            <!-- Purchase no -->
            <v-col cols="3" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">Purchase no</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.purchase_no }}</div>
                </div>
              </div>
            </v-col>

            <!-- Date -->
            <v-col cols="3" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">Date</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.date | moment('DD-MM-YYYY') }}</div>
                </div>
              </div>
            </v-col>

            <!-- Invoice no -->
            <v-col cols="3" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">Invoice no</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.invoice_no ? record.invoice_no : '-' }}</div>
                </div>
              </div>
            </v-col>

            <!-- Order no -->
            <v-col cols="3" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">Order no</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.order_no ? record.order_no : '-'}}</div>
                </div>
              </div>
            </v-col>

          </v-row>

          <!-- Parties -->
          <v-row align="top" no-gutters class="mt-3">
            <!-- From -->
            <v-col cols="6" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">From account</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.fromName }}</div>
                  <div>
                    {{ record.fromAddress }}
                    <span v-if="record.fromContact1 || record.fromContact2"> - </span>
                    <span>{{ record.fromContact1 }}</span>
                    <span v-if="record.fromContact1 && record.fromContact2"> , </span>
                    <span>{{ record.fromContact2 }}</span>
                  </div>
                </div>
              </div>
            </v-col>

            <!-- To -->
            <v-col cols="6" class="px-2">
              <div class="px-3 py-1"
                :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">To godown</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.toName }}</div>
                  <div>
                    {{ record.toAddress }}
                    <span v-if="record.toContact1 || record.toContact2"> - </span>
                    <span>{{ record.toContact1 }}</span>
                    <span v-if="record.toContact1 && record.toContact2"> , </span>
                    <span>{{ record.toContact2 }}</span>
                  </div>
                </div>
              </div>
            </v-col>
          </v-row>

          <!-- Products -->
          <table class="invoice-record-table mt-6 elevation-1 rounded">
            <tr :class="$vuetify.theme.dark ? 'blue-grey darken-4' : 'blue-grey lighten-4'">
              <th class="text-left left-round-top">#</th>
              <th class="text-left">Product</th>
              <th class="text-right">Lot no</th>
              <th class="text-right">Rent</th>
              <th class="text-right">Loading</th>
              <th class="text-right">Unloading</th>
              <th class="text-right">Quantity (Nos)</th>
              <th class="text-left"></th>
              <th class="text-right">Quantity (Kgs)</th>
              <th class="text-left right-round-top"></th>
            </tr>

            <tr v-for="(product, index) in recordProducts" :key="index"
              :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
              <td class="border text-left"
                :class="(index == recordProducts.length - 1) ? 'left-round-bottom' : ''">
                {{ index + 1 }}
              </td>
              <td class="border text-left font-weight-bold">{{ product.name }}</td>
              <td class="border text-right font-weight-bold">{{ product.lotNumber ? product.lotNumber : '-' }}</td>
              <td class="border text-right">{{ product.rent ? formatQuantity(product.rent, 1) : '-' }}</td>
              <td class="border text-right">{{ product.loading ? formatQuantity(product.loading, 1) : '-' }}</td>
              <td class="border text-right">{{ product.unloading ? formatQuantity(product.unloading, 1) : '-' }}</td>
              <td class="border text-right font-weight-bold">{{ formatQuantity(product.quantity, 2) }}</td>
              <td class="border">
                {{ product.unit }}<span class="subtitle-2 pl-1">({{ formatQuantity(product.packing, 0) }})</span>
              </td>
              <td class="border text-right font-weight-bold">{{ formatQuantity(product.quantityKgs, 2) }}</td>
              <td class="border">KGS</td>
            </tr>
          </table>

          <!-- Additional -->
          <v-row align="top" class="mt-6" no-gutters>
            <!-- Agent -->
            <v-col cols="3" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">Agent</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.agentName ? record.agentName : '-' }}</div>
                </div>
              </div>
            </v-col>

            <!-- Transport details -->
            <v-col cols="3" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">Transport details</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.transport_details ? record.transport_details : '-' }}</div>
                </div>
              </div>
            </v-col>

            <!-- Remarks -->
            <v-col cols="6" class="px-2">
              <div class="px-3 py-1" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                <div class="font-weight-bold grey--text" :class="$vuetify.theme.dark ? '' : 'text--darken-1'">Remarks</div>
                <div class="px-4 py-1">
                  <div class="font-weight-bold">{{ record.remarks ? record.remarks : '-' }}</div>
                </div>
              </div>
            </v-col>
          </v-row>
        </div>

        <v-card-actions class="d-flex justify-space-between">
          <div class="d-flex">
            <v-btn :color="$vuetify.theme.dark ? 'primary' : 'indigo'" text
              @click="editFromTable({ name: 'purchases.action', params: { id: record.id } })">
                <v-icon class="text-h6 mr-2">mdi-circle-edit-outline</v-icon> edit purchase
            </v-btn>

            <div class="grey--text text--lighten-1 mx-2 font-weight-thin" style="font-size: 1.5rem">|</div>

            <!-- PDF -->
            <v-btn color="error" text tabindex="-1" :disabled="disableExport"
              @click="disableExportButtons(2)"
              :href="`/exports/pdf/purchases/${record.id}`"
              :download="`${apiRoute}.pdf`">
                <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
            </v-btn>

            <!-- Print -->
            <v-btn @click="disableExportButtons(2); printPage('print-record', `/exports/print/purchases/${record.id}`)"
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
import { CommonMixin } from '../../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  mounted() {
    this.sortBy = 'date'
    this.apiRoute = 'sales'

    this.loadRecords()
    this.fetchAgentAutofill()
    this.fetchToGodownAutofill()
    this.fetchFromGodownAutofill()

    this.setupExtraColumns([
      { value: 'agent', label: 'Agent' },
      { value: 'remarks', label: 'Remarks' },
      { value: 'created_at', label: 'Created at' },
    ])
  },

  components: {
    AppBar: require('../../../components/AppBar').default
  },

  data() {
    return {
      agent_FILTER: false,
      agents: [],
      agentSelectOnlyId: [],
      agentSelectExceptId: [],

      fromGodown_FILTER: false,
      fromGodowns: [],
      fromGodownSelectOnlyId: [],
      fromGodownSelectExceptId: [],

      toGodown_FILTER: false,
      toGodowns: [],
      toGodownSelectOnlyId: [],
      toGodownSelectExceptId: [],

      invoiceNo: '',
      invoiceNo_FILTER: false,
    }
  },

  methods: {
    fetchAgentAutofill() {
      this.filterLoading = true
      this.axios.get('/api/autofills/agents/with_transactions')
        .then(response => {
          this.agents = response.data
          this.filterLoading = false
        })
    },

    fetchToGodownAutofill() {
      this.filterLoading = true
      this.axios.get('/api/autofills/godowns/to_with_transactions/3')
        .then(response => {
          this.toGodowns = response.data
          this.filterLoading = false
        })
    },

    fetchFromGodownAutofill() {
      this.filterLoading = true
      this.axios.get('/api/autofills/godowns/from_with_transactions/3')
        .then(response => {
          this.fromGodowns = response.data
          this.filterLoading = false
        })
    },
  }
}
</script>
