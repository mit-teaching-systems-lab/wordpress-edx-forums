const TSV = require('tsv');
const fs = require('fs');
const _ = require('lodash');

// Read in file
const rows = TSV.parse(fs.readFileSync(process.argv[2]).toString().trim());
const FIELDS = {
 USERNAME_KEY: 'SHA2(user_login, 256)',
 PARENT_POST_TITLE: 'parent_post_title'
};

// Group by users
const byUsers = _.groupBy(rows, FIELDS.USERNAME_KEY);

// Get all assignment names
const postParents = _.uniq(_.map(rows, FIELDS.PARENT_POST_TITLE).filter(value => value !== 'NULL'));

// Convert into flat table with columns based on assignment names
const outputRows = Object.keys(byUsers).map((userKey) => {
  const userRows = byUsers[userKey];
  const outputRow = postParents.reduce((map, parentPostTitle) => {
    const userRow = _.find(userRows, row => row[FIELDS.PARENT_POST_TITLE] === parentPostTitle)
    map[parentPostTitle] = (userRow) ? 1 : '';
    return map;
  }, {});
  outputRow[FIELDS.USERNAME_KEY] = userKey;

  return outputRow;
});

// Homemade TSV output
const outputKeys = [FIELDS.USERNAME_KEY].concat(postParents);
console.log(outputKeys.join("\t") + "\n");
console.log(outputRows.map(row => outputKeys.map(key => row[key]).join("\t")).join("\n"));