export const LoaderMixin = {
  data() {
    return {
      attrs: {
        boilerplate: true,
        elevation: 2,
      },

      searchLoading: false,
      loadMoreLoading: false,

      refreshLoading: false,
      showRecordLoading: false,
      recordsTableLoading: false,
      createButtonLoading: false,
      updateButtonLoading: false,
      deleteButtonLoading: false,

      addRecordsTableLoading: false,

      dft_ShowRecord: 'showRecordLoading',
      dft_RecordsTable: 'recordsTableLoading',
      dft_CreateButton: 'createButtonLoading',
      dft_UpdateButton: 'updateButtonLoading',
      dft_DeleteButton: 'deleteButtonLoading',

      dialogCreateButton: false,
      dialogUpdateButton: false,
    }
  }
}
