export default {
  body: [
    {
      property: 'location',
      type: 'string',
      description: 'The location to browse',
    },
  ],
  response: {
    name: 'array<File|Folder>',
    route: '/data.html',
  },
}