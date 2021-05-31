<template>
  <div>
    <AppBar backRoute="home" />


    <!-- Main Col Wrapper -->
    <v-col cols="12" lg="11" class="mx-auto">

      <div class="text-h5 py-4">Purchases</div>

      <!-- Above Table -->
      <v-row align="end">
        <!-- Left Section -->
        <v-col cols="12" sm="12" md="6" class="text-h5 d-flex">

          <!-- New record -->
          <v-btn color="indigo" dark :to="{ name: 'purchases.action' }"
            v-shortkey="['alt', 'n']" @shortkey.once="$router.push({ name: 'purchases.action' })">
              <v-icon class="mr-1 subtitle-1">mdi-plus</v-icon>
              new purchase
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
            :href="`/exports/pdf/purchases?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.pdf`">
              <v-icon class="text-h6 mr-2">mdi-file-pdf</v-icon> PDF
          </v-btn>

          <!-- Excel -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px" :disabled="disableExport || records.length == 0"
            @click="disableExportButtons()" :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'success--text' : 'white success--text'"
            :href="`/exports/excel/purchases?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`"
            :download="`${apiRoute}.xlsx`">
              <v-icon class="text-h6 mr-2">mdi-file-excel</v-icon> excel
          </v-btn>

          <!-- Print -->
          <v-btn tabindex="-1" class="ml-2" style="width: 120px"
            :loading="refreshLoading"
            :color="$vuetify.theme.dark ? 'primary--text' : 'white indigo--text'"
            :disabled="disableExport || records.length == 0" @click="disableExportButtons();
              printPage('all-print', `/exports/print/purchases?query=${query}&sortBy=${sortBy}&flow=${flow}&${customQuery}`)">
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

                <!-- Invoice No -->
                <th class="subtitle-2 text-center" :class="sortBy == 'invoiceNo' ? 'pink--text font-weight-bold' : ''"
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
                    <span class="sort-link" @click="sortRecords('fromName', sortBy)">From account</span>
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
                    <span class="sort-link" @click="sortRecords('toName', sortBy)">To godown</span>
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

                  <td class="subtitle-1 text-center grey--text font-weight-bold"
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
      </div> <!-- / Table section end -->

    </v-col> <!-- / Main Col Wrapper end -->


    <!-- View Record Dialog -->
    <v-dialog v-model="viewRecordDialog" max-width="90%">
      <v-card>
        <v-card-title class="headline d-flex justify-space-between align-center">
          <div>Purchase Details</div>
          <v-btn icon @click="viewRecordDialog = false"><v-icon>mdi-close</v-icon></v-btn>
        </v-card-title>

        <v-card-text class="pt-6 pb-10">
          <v-row>
            <v-col cols="12" md="8" class="pr-10">
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
                  <td class="subtitle-1 font-weight-bold text-left">Products</td>
                  <td class="subtitle-1" :class="$vuetify.theme.dark ? 'white--text' : 'grey--text text--darken-2'">

                    <div class="rounded px-2" :class="$vuetify.theme.dark ? 'grey darken-3' : 'grey lighten-4'">
                      <table style="width: 100%">
                        <tr>
                          <th class="text-left"><small>Name</small></th>
                          <th class="text-center"><small>Lot no</small></th>
                          <th class="text-right"><small>C Quantity</small></th>
                          <th class="text-right"><small>Quantity</small></th>
                        </tr>

                        <tr v-for="(product, index) in recordProducts" :key="index">
                          <td style="border: none; padding: 1px 0">
                            <span>{{ product.name }}</span>
                          </td>
                          <td style="border: none; padding: 1px 0;" class="px-2 text-center">
                            <span v-if="product.lotNumber" class="subtitle-2" :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                              ({{ product.lotNumber }})
                            </span>
                            <span v-else>-</span>
                          </td>
                          <td style="border: none; padding: 1px 0" class="text-right pl-5">
                            <span v-if="product.compoundUnit && product.compoundQuantity">
                              <span class="font-weight-bold">{{ formatQuantity(product.compoundQuantity, 0) }}</span>
                              <span class="ml-1 subtitle-2" :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                                {{ product.compoundUnit }}
                              </span>
                            </span>
                            <span v-else>-</span>
                          </td>
                          <td style="border: none; padding: 1px 0" class="text-right pl-5">
                            <span class="font-weight-bold">{{ formatQuantity(product.quantity) }}</span>
                            <span class="ml-1 subtitle-2" :class="$vuetify.theme.dark ? '' : 'text--darken-2'">
                              {{ product.unit }}
                            </span>
                          </td>
                        </tr>
                      </table>
                    </div>

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

            <v-col cols="12" md="4">
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
                    {{ record.eway_bill_no ? formatEwayNo(record.eway_bill_no) : '-' }}
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
    this.apiRoute = 'purchases'

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
      this.axios.get('/api/autofills/godowns/to_with_transactions/2')
        .then(response => {
          this.toGodowns = response.data
          this.filterLoading = false
        })
    },

    fetchFromGodownAutofill() {
      this.filterLoading = true
      this.axios.get('/api/autofills/godowns/from_with_transactions/2')
        .then(response => {
          this.fromGodowns = response.data
          this.filterLoading = false
        })
    },
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
