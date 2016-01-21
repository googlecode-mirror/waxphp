# History #

Starting in 2005, I was doing several development jobs for my high school.  Across all of the applications there were several common tasks that I wanted to expedite.  At the time I wasn't really aware of any PHP frameworks.  As I continued working on projects, I began building a library of scripts, PHP objects, and stylesheets that I could use across all of my applications.  It was this original library that sparked the very beginning of the Wax framework.

### CGL ###
CGL, or Common Graphics Library as I called it, was a folder that contained all of the files that I used across my applications, including several scripts, stylesheets, and PHP objects, including an Active Directory interaction class and several custom functions.

### CGL 2 ###
CGL2 was an extension of CGL which included a few custom classes that I created that could be extended to create simple PHP controls that could be reused - like a Image Thumbnailing class and a MenuBar class.

### PAD ###
To solve the problem of authenticating across all of these systems we created a very very simple system dubbed Pseudo Active Directory.  The name didn't really describe the function, although it did provide a decent single sign-on solution for the school.  Performance was terrible though, and it didn't work very well in a production environment.

### CompactIS ###
The Compact Information System was developed to solve the problems with PAD.  It used a vast library of interfaces and classes to provide an extensible single sign-on and access control list solution.  The system itself worked quite well, except it was developed mostly by one of my colleagues, who unfortunately left very little documentation on the project.  In addition, the project utilized cutting-edge PHP features that would not be widely portable across different versions.

### WISP PHP Framework ###
After a little more looking into web standards and technologies, I decided to bundle all of the previous projects I had worked on into a single PHP framework, entitled WISP: Websites with Integrated Scripts and PHP.  The project was promising but had a pretty messy codebase due to the several design changes encountered during development.

### Wax PHP Framework ###
Upon cleaning up the WISP code, I realized that the framework I had was considerably different than the original ideas I had.  As a result, I renamed the framework to Wax: Websites and X, where X is intended to be any web technology.  The framework is designed to be highly extensible and useable without the hassles of a standard MVC framework.  This results in even more rapid prototyping than projects like CakePHP, Symfony, and Ruby on Rails (not PHP but the same MVC concept).