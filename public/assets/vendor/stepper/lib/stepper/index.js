require.paths.unshift(__dirname + '/../../dep');

var Step = require('step'),
    underscore = require('underscore');

Stepper = exports.Stepper = function() {

    var steps = [];
    var self = this;

    this.add = function(step) {
        if (step instanceof Stepper || step instanceof Grouper) {
            steps.push(function(err, val) {
                if (err) throw err;
                step.walk(this);
            });
        } else {
            steps.push(step);
        }
        return this;
    };
    
    this.walk = function(fn) {
        fn && steps.push(fn);
        Step.apply(null, steps);
    };
    
};

Grouper = exports.Grouper = function() {

    var steps = [];
    var self = this;

    this.add = function(step) {
        steps.push(step);
        return this;
    };
    
    this.walk = function(fn) {
        var groupSteps = [];
        groupSteps.push(function prime() {
            return 1;
        });
        groupSteps.push(function(err, val) {
            if (err) { throw err; }
            var group = this.group();
            _(steps).each(function(step) {
                step(group());
            });
        });
        fn && groupSteps.push(fn);
        Step.apply(null, groupSteps);
    };
};
