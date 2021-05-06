<template>
  <div>
    <AppBar />

    <v-col cols="12" md="9" class="mx-auto">

      <!-- Transfers -->
      <div class="overline mt-5 mb-3 ml-2" style="font-size: 1rem !important"
        :class="$vuetify.theme.dark ? 'grey--text' : 'grey--text text--darken-1'">
          Transfers
      </div>

      <!-- Transfer Links -->
      <v-row>
        <v-col cols="12" md="6" lg="4"
          v-for="link in transferLinks" :key="link.label">
          <v-card elevation="1" v-ripple class="mx-1"
            @keypress.enter="$router.push({ name: link.routeName })"
            @click="$router.push({ name: link.routeName })">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title class="text-h6 mb-1">{{ link.label }}</v-list-item-title>
                  <v-list-item-subtitle>{{ link.description }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-avatar tile size="65" :color="link.color" class="rounded elevation-2">
                  <v-icon class="white--text text-h3">{{ link.icon }}</v-icon>
                </v-list-item-avatar>
              </v-list-item>
          </v-card>
        </v-col>
      </v-row>


      <!-- Masters -->
      <div class="overline mt-10 mb-3 ml-2" style="font-size: 1rem !important"
        :class="$vuetify.theme.dark ? 'grey--text' : 'grey--text text--darken-1'">
          masters
      </div>

      <!-- Master Links -->
      <v-row>
        <v-col cols="12" md="6" lg="4"
          v-for="link in masterLinks" :key="link.label">
          <v-card elevation="1" v-ripple class="mx-1"
            @keypress.enter="$router.push({ name: link.routeName })"
            @click="$router.push({ name: link.routeName })">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title class="text-h6 mb-1">{{ link.label }}</v-list-item-title>
                  <v-list-item-subtitle>{{ link.description }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-avatar tile size="65" :color="link.color" class="rounded elevation-2">
                  <v-icon class="white--text text-h3">{{ link.icon }}</v-icon>
                </v-list-item-avatar>
              </v-list-item>
          </v-card>
        </v-col>
      </v-row>


      <!-- Reports -->
      <div class="overline mt-10 mb-3 ml-2" style="font-size: 1rem !important"
        :class="$vuetify.theme.dark ? 'grey--text' : 'grey--text text--darken-1'">
          reports
      </div>

      <!-- Report Links -->
      <v-row>
        <v-col cols="12" md="6" lg="4"
          v-for="link in reportLinks" :key="link.label">
          <v-card elevation="1" v-ripple class="mx-1"
            @keypress.enter="$router.push({ name: link.routeName })"
            @click="$router.push({ name: link.routeName })">
              <v-list-item three-line>
                <v-list-item-content>
                  <v-list-item-title class="text-h6 mb-1">{{ link.label }}</v-list-item-title>
                  <v-list-item-subtitle>{{ link.description }}</v-list-item-subtitle>
                </v-list-item-content>

                <v-list-item-avatar tile size="65" :color="link.color" class="rounded elevation-2">
                  <v-icon class="white--text text-h3">{{ link.icon }}</v-icon>
                </v-list-item-avatar>
              </v-list-item>
          </v-card>
        </v-col>
      </v-row>

    </v-col>

  </div>
</template>

<script>
import { CommonMixin } from '../mixins/CommonMixin'

export default {
  data() {
    return {
      transferLinks: [
        {
          routeName: 'purchases.index',
          label: 'Purchase',
          description: 'Stock transfer through purchase of products.',
          icon: 'mdi-cart-arrow-down',
          color: 'error'
        },
        {
          routeName: 'inter_godowns.index',
          label: 'Inter Godown',
          description: 'Transfer throught internal movement of products.',
          icon: 'mdi-atom-variant',
          color: 'indigo'
        },
        {
          routeName: 'sales.index',
          label: 'Sales',
          description: 'Stock transfer throught sale of products.',
          icon: 'mdi-cart-arrow-up',
          color: 'green accent-4'
        },
      ],

      masterLinks: [
        {
          routeName: 'products.index',
          label: 'Products',
          description: 'Products that your business deals in.',
          icon: 'mdi-shape',
          color: 'brown lighten-1'
        },
        {
          routeName: 'godowns.index',
          label: 'Godowns',
          description: 'Owned / rented godowns, shops and cold storages.',
          icon: 'mdi-store',
          color: 'brown lighten-1'
        },
        {
          routeName: 'accounts.index',
          label: 'Accounts',
          description: 'Supplier and sustomer accounts.',
          icon: 'mdi-account-group',
          color: 'brown lighten-1'
        },
        {
          routeName: 'agents.index',
          label: 'Agents',
          description: 'Contact persons for purchase and sales.',
          icon: 'mdi-handshake',
          color: 'brown lighten-1'
        },
      ],

      reportLinks: [
        {
          routeName: 'reports.index',
          label: 'Reports',
          description: 'Godowns, accounts, products and stock reports.',
          icon: 'mdi-chart-arc',
          color: 'amber accent-4'
        },
      ]
    }
  },

  mixins: [CommonMixin],

  components: {
    AppBar: require('../components/AppBar').default
  }
}
</script>

