const routes = [
  {
    name: 'home',
    path: '/',
    component: require('../../views/pages/Home').default
  },

  // GODOWNS
  {
    name: 'godowns.index',
    path: '/godowns',
    component: require('../../views/pages/godowns/Index').default
  },
  {
    name: 'godowns.action',
    path: '/godowns/action/:id?',
    component: require('../../views/pages/godowns/Action').default
  },

  // ACCOUNTS
  {
    name: 'accounts.index',
    path: '/accounts',
    component: require('../../views/pages/accounts/Index').default
  },
  {
    name: 'accounts.action',
    path: '/accounts/action/:type/:id?',
    component: require('../../views/pages/accounts/Action').default
  },

  // PRODUCTS
  {
    name: 'products.index',
    path: '/products',
    component: require('../../views/pages/products/Index').default
  },
  {
    name: 'products.action',
    path: '/products/action/:id?',
    component: require('../../views/pages/products/Action').default
  },

  // AGENTS
  {
    name: 'agents.index',
    path: '/agents',
    component: require('../../views/pages/agents/Index').default
  },
  {
    name: 'agents.action',
    path: '/agents/action/:id?',
    component: require('../../views/pages/agents/Action').default
  },

  // PURCHASES
  {
    name: 'purchases.index',
    path: '/purchases',
    component: require('../../views/pages/transfers/purchases/Index').default
  },
  {
    name: 'purchases.action',
    path: '/purchases/action/:id?',
    component: require('../../views/pages/transfers/purchases/Action').default
  },

  // SALES
  {
    name: 'sales.index',
    path: '/sales',
    component: require('../../views/pages/transfers/sales/Index').default
  },
  {
    name: 'sales.action',
    path: '/sales/action/:id?',
    component: require('../../views/pages/transfers/sales/Action').default
  },

  // INTER GODOWNS
  {
    name: 'inter_godowns.index',
    path: '/inter_godowns',
    component: require('../../views/pages/transfers/inter_godowns/Index').default
  },
  {
    name: 'inter_godowns.action',
    path: '/inter_godowns/action/:id?',
    component: require('../../views/pages/transfers/inter_godowns/Action').default
  },

  // Reports
  {
    name: 'reports.index',
    path: '/reports',
    component: require('../../views/pages/reports/Index').default
  },

  // Stock Reports
  {
    name: 'reports.godown_products_stock',
    path: '/reports/godown_products_stock',
    component: require('../../views/pages/reports/stock/GodownProductsStock').default
  },
  {
    name: 'reports.products_stock',
    path: '/reports/products_stock',
    component: require('../../views/pages/reports/stock/ProductsStock').default
  },
  {
    name: 'reports.products_lot_stock',
    path: '/reports/products_lot_stock',
    component: require('../../views/pages/reports/stock/ProductsLotStock').default
  },

  // Movement Reports
  {
    name: 'reports.product_movements',
    path: '/reports/product_movements',
    component: require('../../views/pages/reports/movement/ProductMovements').default
  },
  {
    name: 'reports.account_movements',
    path: '/reports/account_movements',
    component: require('../../views/pages/reports/movement/AccountMovements').default
  },
  {
    name: 'reports.godown_movements',
    path: '/reports/godown_movements',
    component: require('../../views/pages/reports/movement/GodownMovements').default
  },

  // Transfer Reports
  {
    name: 'reports.agent_transfers',
    path: '/reports/agent_transfers',
    component: require('../../views/pages/reports/transfers/AgentTransfers').default
  },
  {
    name: 'reports.all_transfers',
    path: '/reports/all_transfers',
    component: require('../../views/pages/reports/transfers/AllTransfers').default
  },
]

export default routes
