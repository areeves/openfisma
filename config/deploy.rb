set :application, "openfisma"
set :repository,  "https://endeavor.devguard.com/svn/openfisma/trunk"
set :user, "rails"
ssh_options[:port] = 2200
set :use_sudo, true
set :mongrel_conf, "/etc/mongrel_cluster/openfisma.yml"

# If you aren't deploying to /u/apps/#{application} on the target
# servers (which is the default), you can specify the actual location
# via the :deploy_to variable:
set :deploy_to, "/home/rails/#{application}"

# If you aren't using Subversion to manage your source code, specify
# your SCM below:
# set :scm, :subversion

role :app, "openfisma.dynalias.org"
role :web, "openfisma.dynalias.org"
role :db,  "openfisma.dynalias.org", :primary => true

desc "Restarts the Mongrel Application Cluster"
task :restart_app_server, :roles => :app do
   sudo "/etc/init.d/mongrel_cluster restart"
end
  
desc "Restarts the Apache Web Server"
task :restart_web_server, :roles => :web do
  sudo "/etc/init.d/apache2 restart"
end

after "deploy:start", :restart_app_server, :restart_web_server