export default {
  body: [
    {
      property: 'file',
      type: 'file',
      required: 'true',
      description: 'The attachment file to be uploaded',
    },
  ],
  response: {
    name: 'Attachment',
    route: '/data.html#attachment',
  },
}