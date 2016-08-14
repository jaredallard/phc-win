/**
 * Abstraction layer for all phc operations.
 *
 * @author Jared Allard <jaredallard@outlook.com>
 * @license MIT
 * @version 1.0.0
 **/

'use strict';

const debug  = require('debug')('phc:abstraction');
const os     = require('os');
const path   = require('path');
const crypto = require('crypto');
const async  = require('async');
const fs     = require('fs');

/**
 * phc Abstraction
 *
 * @class Phc
 **/
class Phc {
  constructor() {
    debug('initialized');
  }

  /**
   * Return a random string
   *
   * @param {Function} cb - callback
   * @returns {null} use cb
   **/
  _getRandomString(cb) {
    crypto.randomBytes(48, (err, buf) => {
      return cb(err, buf.toString('hex'));
    });
  }

  /**
   * Prepare the build environment
   *
   * @param {Function} cb - callback
   * @returns {undefined} use cb
   **/
  prepare(cb) {
    const TMP = os.tmpdir();

    let dir = null;
    async.waterfall([
      (next) => {
        // find the dir name.
        this._getRandomString((err, string) => {
          if(err) return next(err);

          dir = path.join(TMP, string);

          if(fs.existsSync(dir)) {
            debug('prepare:dir', 'already exists!?!?!?!?!');
          }

          // there could be some cases where it won't be able to make the dir.
          fs.mkdirSync(dir);
          debug('prepare:dir', 'set to', dir);
        })
      },

      (next) => {

      }

    ], err => {
      if(err) {
        console.error(err);
        process.exit(1);
      }
      return cb(err);
    })
  }
}

module.exports = Phc;
