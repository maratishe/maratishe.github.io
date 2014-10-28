<style> 
em { font-size:larger; font-weight: bold; font-style: normal; text-decoration: none; } 
strong { font-size:larger; font-weight: bold; text-decoration: none; }
h1 { margin: 40px 0px 8px 0px; font-size: 20px; font-weight: bold; color: #000; padding: 3px 3px; border-bottom: 2px solid #000; background-color: #ddd;}
em code { background-color:#555; color:#fff; padding: 2px; padding-left: 4px; padding-right: 4px; border-radius: 4px; font-family: courier; }
code { background-color:#299; color:#fff; padding: 2px; padding-left: 4px; padding-right: 4px; border-radius: 4px; font-family: courier; }
pre { background-color:#299; color:#fff; padding: 2px; padding-left: 4px; padding-right: 4px; border-radius: 4px; font-family: courier;}
hr { border: 1px solid #999; margin: 5px 0px 10px; }
blockquote { border-left: 2px solid #555; margin: 5px 0px 5px 20px; padding: 3px 5px; color: #36A; }
a { color:#f00; font-weight: bold; text-decoration: underline; }
hdr { font-size: 22px; font-weight: bold; display:block;} /* headers */
pass { font-size: 14px; font-weight: bold; background-color:#49F; color:#fff; padding: 2px; padding-left: 4px; padding-right: 4px; border-radius: 4px;} /* passwords */
uline { font-size:larger; font-weight: bold; font-style: normal; text-decoration: none; background-color:#ccc; }
tr { display: block; position: relative; clear: both; height: 3px; }
img { width: 60%; float: right; height: auto; border:0px; }
s1 { font-size:larger; font-weight: bold; font-style: normal; text-decoration: none; background-color:#ccc; }
s2 { font-size:larger; font-weight: bold; font-style: normal; text-decoration: none; background-color:#EF4; font-family: courier; } /* courier yellow bg */
</style>

<span style="font-size:smaller;">[back to maratishe.github.io](http://maratishe.github.io/)</span>
<hdr>Installing Hadoop into multiple Fedora 20 machines</hdr>
Author: <s2>maratishe@gmail.com</s2> -- created <s1>140710</s1>

Actually, the title should have said **...into mutiple FC20 VMs** but the setup will work on physical machines as well.  If you're testing, then there are several reasons why you actually want to test the Hadoop out in virtual environment.  The biggest one is *being able to go back and repeat step(s) when you fail or otherwise need to start over*. 

I did not arrive at this solution by myself. I built the [mini XCP cloud based on MacMini](http://tinyurl.com/minicsBuild) myself  but [Hadoop installation](http://hackecho.com/2014/04/hadoop-cluster-setup-instruction-with-fedora-20/) and [the WordCount example MapReduce job](http://timothysc.github.io/blog/2013/09/14/hadoop-mapreduce/) were adopted from instructions published by these other two sources.  I do clarify several points which were vague in the original source, in this manual.


Quick Jumps:

1. <a href="#makevm">Making a Standard FC20 VM</a>
2. <a href="#preparevm">Preparing the VM: Users, Passwordless SSH</a>
3. <a href="#deploy">Deploy Hadoop on Multiple VMs</a>
4. <a href="#run">Run an Example MapReduce Job (WordCount)</a>


<span id="makevm"></span>
# Making a Standard FC20 VM

It is not hard to build a standard FC20 VM. I use a [MacMini based XCP1.6 cloud](http://tinyurl.com/minicsBuild) which can host up to 7 VMs. I use `xe` command line for general management but new VMs are easier installed from *XenServer GUI*.  You can use a very small [Fedora-20-x86_64-netinst.iso](https://dl.fedoraproject.org/pub/fedora/linux/releases/20/Fedora/x86_64/iso/) and [kickstart file](files/kickstart-minimal-fedora20.txt) (you need to host it somewhere -- I normally run a temp PHP webserver from command line).  Your param line in *New VM* GUI is then:

```
minimal utf8 nogpt noipv6 ks=http://YOUR.HOST.PATH/kickstart-minimal-fedora20.txt
```

When ready, it is best if you `vm-export` it using the `xe` CLI into an `.xva` file and store it somewhere save. It will take about *2Gbytes* and its use via `vm-import` will take 2-5 minutes on average, but it is still worth to install new VMs from ready `.xva` templates than do a fresh install each time. 


<span id="preparevm"></span>
# Preparing the VM: Static IP, Users, Passwordless SSH

If you followed the above instructions, then you have a minimum installation. Bring up the minimum development tools (one line) before walking the preparation procedure below:
```
yum install git cmake build-essential libgcrypt11-dev libjson0-dev libcurl4-openssl-dev libexpat1-dev libboost-filesystem-dev libboost-program-options-dev binutils-dev net-tools java php php-mbstring openssl
```

**(1) Static IP** You have to make sure your VM has *static IP* even if you have DHCP on your network.  This will make automation very easy later on.  I will not show you how to write automation scripts, but will give some hints in that direction.  To set a *static IP* run these as `root` (replace large caps with your information):

```
ifdown eth0
ifup eth0
ifconfig eth0 IPADDRESS netmask MASK
route add default gw GATEWAY
echo "nameserver NAMESERVER" > /etc/resolv.conf
```

**(2) Users** Your VM probably has only `root`. Add a normal user but make it a *sudoer* with `usermod -G wheel ACCOUNT`.  We do not have to care about hadoop users as those are installed automatically by `yum` later on.

**(3) Passwordless SSH** This is actually where VMs are better than physical machines. On The latter you have to send keys in all-to-all combinations (unless you clone your machines).  In VMs, we exchange keys once and they will stay in effect when Hadoop runs on multiple machines. 

To get the key (each for root and ACCOUNT users) -- the command will try to interact with you, just ignore it by clicking SPACES 3 or 4 times:
```
ssh-keygen -t rsa
```
You have your public key in `~/.ssh/id_rsa.pub`. You need to add it to authorized keys to another user using: 
```
cat PATH.TO.A's.KEY >> B's.HOME/.ssh/authorized_keys
```
With *A* being root and *B* being the newly created sudoer account, you need to exchange the keys in all combinations: *AA*, *AB*, *BA*, and *BB*.  Given that *B* does not have access to root's `~/.ssh/`, that one key you will have to copy somewhere else. 

SSH is picky about access permissions on keys, so, for each user run:
```
chmod 600 ~/.ssh/authorized_keys
```
Finally, just to be sure, uncomment the following two lines in `/etc/ssh/sshd_config` (as root):
```
RSAAuthentication yes
PubkeyAuthentication yes
```

This work does not take too much, but if you like, you have take a `.xva` snapshot at this stage as well. I keep these for projects other than Hadoop.


<span id="install"></span>
# Preparing the VM: Installing Hadoop

Finally, *install Hadoop* -- you need to have your network set up and running to be able to do that:
```
yum install hadoop-common hadoop-common-native hadoop-hdfs hadoop-mapreduce hadoop-mapreduce-examples hadoop-yarn
```

This will change your VM considerably -- install new users, Hadoop and MapReduce software, etc.  Similarly, the following step will introduce irreversible changes. So, it is best if you take an `.xva` snapshot of this state. Mine are about *3Gbytes* in size.



<span id="deploy"></span>
# Deploy Hadoop on Multiple VMs

So, at this point we have a VM template which has Hadoop and has been prepared **but not yet configured** for Hadoop use.  This is what we will do here.

Just to be clear, on my side I use a script to accomplish all the below work in one big swoop.  I use a *text-hash* notation for that. All the below stuff is in this notation.

Let us assume that we have *3 VMs*: one *master* and two *data* VMs (*data1* and *data2*) which I can define as:
```
master=192.168.0.10,data1=192.168.0.11,data2=192.168.0.12
```
Hadoop in recent versions is picky about hostnames and IPs which is why each machine has to register the symbolic names for each other (you can completely replace current contents of all files):

 * own name to `/etc/hostname` (just the name)  and `HOSTNAME=master` to `/etc/sysconfig/network` (replace *master* with *data1* and others in other VMs)
 * mapping of names to IPs for all VMs in format `IP [tab] NAME` per line to `/etc/hosts`




<span id="run"></span>
# Run an Example MapReduce Job (WordCount)





<br><hr>
 Written with [dillinger.io](http://dillinger.io) -- a Markdown WYSIWYG for GitHub Pages.
