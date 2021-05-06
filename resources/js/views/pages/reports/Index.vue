<template>
  <div>
    <AppBar backRoute="home" />

    <v-col cols="12" md="11" class="mx-auto">

      <!-- Stock Reports -->
      <div class="overline mt-5 mb-2 ml-2" style="font-size: 1rem !important"
        :class="$vuetify.theme.dark ? 'grey--text' : 'grey--text text--darken-1'">
          stock reports
      </div>

      <v-row>
        <v-col cols="12" md="4"
          v-for="link in stockReports" :key="link.label">
          <v-card elevation="1" class="mx-2">
              <v-list-item three-line class="d-flex align-center">
                <v-list-item-content>
                  <v-list-item-title class="text-h6">{{ link.label }}</v-list-item-title>
                  <v-list-item-subtitle>{{ link.description }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-btn text @click="$router.push({ name: link.routeName })" color="success">
                  <v-icon class="mr-2">mdi-play</v-icon>
                  run report
                </v-btn>
              </v-list-item>
          </v-card>
        </v-col>
      </v-row>

      <!-- Movement Reports -->
      <div class="overline mt-10 mb-2 ml-2" style="font-size: 1rem !important"
        :class="$vuetify.theme.dark ? 'grey--text' : 'grey--text text--darken-1'">
          Movement reports
      </div>

      <v-row>
        <v-col cols="12" md="4"
          v-for="link in movementReports" :key="link.label">
          <v-card elevation="1" class="mx-2">
              <v-list-item three-line class="d-flex align-center">
                <v-list-item-content>
                  <v-list-item-title class="text-h6">{{ link.label }}</v-list-item-title>
                  <v-list-item-subtitle>{{ link.description }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-btn text @click="$router.push({ name: link.routeName })" color="success">
                  <v-icon class="mr-2">mdi-play</v-icon>
                  run report
                </v-btn>
              </v-list-item>
          </v-card>
        </v-col>
      </v-row>


      <!-- Transfer Reports -->
      <div class="overline mt-10 mb-2 ml-2" style="font-size: 1rem !important"
        :class="$vuetify.theme.dark ? 'grey--text' : 'grey--text text--darken-1'">
          Transfer reports
      </div>

      <v-row>
        <v-col cols="12" md="4"
          v-for="link in transferReports" :key="link.label">
          <v-card elevation="1" class="mx-2">
              <v-list-item three-line class="d-flex align-center">
                <v-list-item-content>
                  <v-list-item-title class="text-h6">{{ link.label }}</v-list-item-title>
                  <v-list-item-subtitle>{{ link.description }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-btn text @click="$router.push({ name: link.routeName })" color="success">
                  <v-icon class="mr-2">mdi-play</v-icon>
                  run report
                </v-btn>
              </v-list-item>
          </v-card>
        </v-col>
      </v-row>

    </v-col>
  </div>
</template>

<script>
import { CommonMixin } from '../../mixins/CommonMixin'

export default {
  mixins: [CommonMixin],

  data() {
    return {
      stockReports: [
        {
          routeName: 'reports.products_stock',
          label: 'Product Stock',
          description: 'Product wise stock report'
        },

        {
          routeName: 'reports.godown_products_stock',
          label: 'Godown Product Stock',
          description: 'Godown wise product stock report'
        },

        {
          routeName: 'reports.lot_stock',
          label: 'Lot Stock',
          description: 'Lot wise stock report'
        },

        {
          routeName: 'reports.products_lot_stock',
          label: 'Product Lot Stock',
          description: 'Lot wise product stock report'
        },
      ],

      movementReports: [
        {
          routeName: 'reports.product_movements',
          label: 'Product Movements',
          description: 'Transfer wise product movements'
        },

        {
          routeName: 'reports.account_movements',
          label: 'Account Movements',
          description: 'Transfer wist account movements'
        },

        {
          routeName: 'reports.godown_movements',
          label: 'Godown Movements',
          description: 'Godown movement report'
        },
      ],

      transferReports: [
        {
          routeName: 'reports.agent_transfers',
          label: 'Agent Transfers',
          description: 'Transfers via associated agent'
        },

        {
          routeName: 'reports.all_transfers',
          label: 'All Transfers',
          description: 'Purchase, sales and inter godown transfers'
        },
      ]
    }
  },

  components: {
    AppBar: require('../../components/AppBar').default
  }
}
</script>

