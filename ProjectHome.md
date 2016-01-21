Evolved from the WISP PHP Framework Core (development now discontinued and moved to this project) - it is designed as a rapid prototyping PHP framework that does not require all the hassles of a traditional PHP MVC framework.  Wax involves no command-line interaction and uses a control library to build websites.

Wax is made possible through the [Rensselaer Center for Open Source Software](http://rcos.cs.rpi.edu) and through the generosity of Mr. Sean O'Sullivan.

# Getting Started #

Take a look at the GettingStarted documentation to learn how to build a (very) basic blogging application using Wax.  It's still a very early example of what the framework is capable of, but demonstrates the core functionality.

Additionally, explanations of new features are available from the WaxPHP blog, at http://waxphp.blogspot.com

# DCI: Data Context Interaction #
The new DCI paradigm is at the heart of the Wax framework. To learn more about it, you can read an article written by the creators (Trygve Reenskaug and Jim Coplien) at http://www.artima.com/articles/dci_vision.html.  If you'd like to read even more about it, here's a 74 page paper on it: http://folk.uio.no/trygver/2009/commonsense.pdf

Additionally, the implementation of DCI used for this framework was forked into another project, CoreDCI, available as standalone source at http://code.google.com/p/php-coredci

DCI allows the framework to work in ways far different than a traditional web framework.  The idea is that all application functionality/logic is coded in Role classes.  Contexts call Role methods as necessary depending on the context of the call (and in some cases, Contexts can even be transparent).  This leads to a very flexible runtime network and exciting possibilities for many different aspects of web development.